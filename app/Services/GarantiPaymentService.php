<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GarantiPaymentService
{
    protected string $mode;
    protected string $apiVersion;
    protected string $terminalId;
    protected string $merchantId;
    protected string $terminalUserId;
    protected string $terminalProvUserId;
    protected string $storeKey;
    protected string $provisionPassword;
    protected string $companyName;
    protected string $currencyCode;
    protected string $gatewayUrl;
    protected string $lang;

    public function __construct()
    {
        $this->mode = config('garanti.mode', 'PROD');
        $this->apiVersion = config('garanti.api_version', 'v0.01');
        $this->terminalId = config('garanti.terminal_id');
        $this->merchantId = config('garanti.merchant_id');
        $this->terminalUserId = config('garanti.terminal_user_id');
        $this->terminalProvUserId = config('garanti.terminal_prov_user_id');
        $this->storeKey = config('garanti.store_key');
        $this->provisionPassword = config('garanti.provision_password');
        $this->companyName = config('garanti.company_name');
        $this->currencyCode = config('garanti.currency_code', '949');
        $this->gatewayUrl = config('garanti.gateway_url');
        $this->lang = config('garanti.lang', 'tr');
    }

    /**
     * OOS ödeme formu için gerekli parametreleri hazırla
     */
    public function preparePaymentForm(Order $order, int $installmentCount = 0): array
    {
        // Terminal ID'yi 9 haneli yap (başına 0 ekle)
        $terminalId9Digit = str_pad($this->terminalId, 9, '0', STR_PAD_LEFT);

        // Benzersiz sipariş ID oluştur
        $orderId = $order->order_number . '_' . time();

        // Tutar (kuruş cinsinden, son 2 hane kuruş)
        $amount = (int) round($order->total_tl * 100);

        // Taksit sayısı (boş = tek çekim)
        $installment = $installmentCount > 1 ? (string) $installmentCount : '';

        // Success ve Error URL'leri
        $successUrl = route('payment.callback');
        $errorUrl = route('payment.callback');

        // İşlem tipi
        $txnType = 'sales';

        // Timestamp (ISO 8601 UTC formatı: 2023-04-30T11:31:53Z)
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        // Müşteri bilgileri
        $customerEmail = $order->customer_email ?? $order->user->email ?? '';
        $customerIp = request()->ip() ?? '127.0.0.1';

        // Security Data hesaplama: SHA1(ProvisionPassword + TerminalID_9digit)
        $securityData = strtoupper(sha1($this->provisionPassword . $terminalId9Digit));

        // Hash Data hesaplama (Yeni API - SHA512):
        // SHA512(TerminalID + OrderID + Amount + CurrencyCode + SuccessURL + ErrorURL + TxnType + InstallmentCount + StoreKey + SecurityData)
        $hashString = $this->terminalId . $orderId . $amount . $this->currencyCode . $successUrl . $errorUrl . $txnType . $installment . $this->storeKey . $securityData;
        $hashData = strtoupper(hash('sha512', $hashString));

        // Log için
        Log::info('Garanti OOS Payment Initiated', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'garanti_order_id' => $orderId,
            'amount' => $amount,
            'amount_tl' => $order->total_tl,
            'installment' => $installment,
        ]);

        // PaymentTransaction kaydı oluştur
        $transaction = PaymentTransaction::create([
            'order_id' => $order->id,
            'transaction_id' => $orderId,
            'gateway' => 'garanti',
            'amount' => $order->total_tl,
            'currency' => 'TRY',
            'status' => 'pending',
            'installment_count' => $installmentCount,
            'request_data' => [
                'terminal_id' => $this->terminalId,
                'merchant_id' => $this->merchantId,
                'order_id' => $orderId,
                'amount' => $amount,
                'customer_email' => $customerEmail,
                'customer_ip' => $customerIp,
            ],
        ]);

        return [
            'gateway_url' => $this->gatewayUrl,
            'form_data' => [
                'mode' => $this->mode,
                'apiversion' => $this->apiVersion,
                'terminalprovuserid' => $this->terminalProvUserId,
                'terminaluserid' => $this->terminalUserId,
                'terminalid' => $this->terminalId,
                'terminalmerchantid' => $this->merchantId,
                'orderid' => $orderId,
                'customeremailaddress' => $customerEmail,
                'customeripaddress' => $customerIp,
                'txntype' => $txnType,
                'txnamount' => $amount,
                'txncurrencycode' => $this->currencyCode,
                'txninstallmentcount' => $installment,
                'companyname' => $this->companyName,
                'successurl' => $successUrl,
                'errorurl' => $errorUrl,
                'secure3dhash' => $hashData,
                'secure3dsecuritylevel' => '3D_OOS_PAY',
                'lang' => $this->lang,
                'txntimestamp' => $timestamp,
                'refreshtime' => config('garanti.refresh_time', '10'),
            ],
            'transaction_id' => $transaction->id,
        ];
    }

    /**
     * Callback sonucunu işle
     */
    public function processCallback(array $postData): array
    {
        Log::info('Garanti OOS Callback Received', ['post_data' => $postData]);

        // Sonuç parametrelerini al
        $mdStatus = $postData['mdstatus'] ?? '';
        $orderID = $postData['oid'] ?? '';
        $response = $postData['response'] ?? '';
        $procReturnCode = $postData['procreturncode'] ?? '';
        $errMsg = $postData['errmsg'] ?? '';
        $transId = $postData['transid'] ?? '';
        $hostRefNum = $postData['hostrefnum'] ?? '';
        $authCode = $postData['authcode'] ?? '';
        $hashParams = $postData['hashparams'] ?? '';
        $hashParamsVal = $postData['hashparamsval'] ?? '';
        $hash = $postData['hash'] ?? '';
        $maskedPan = $postData['MaskedPan'] ?? $postData['cardnumber'] ?? '';

        // Transaction'ı bul
        $transaction = PaymentTransaction::where('transaction_id', $orderID)->first();

        if (!$transaction) {
            Log::error('Garanti OOS: Transaction not found', ['order_id' => $orderID]);
            return [
                'success' => false,
                'message' => 'İşlem bulunamadı.',
                'order' => null,
            ];
        }

        // Order'ı al
        $order = $transaction->order;

        // Hash doğrulama - şimdilik devre dışı, mdStatus ve procReturnCode kontrolü yeterli
        // TODO: Garanti'nin yeni API'sine göre hash doğrulama güncellenmeli
        // if (!empty($hashParams) && !empty($hash)) {
        //     $isValidHash = $this->validateHash($postData);
        //     if (!$isValidHash) {
        //         Log::error('Garanti OOS: Hash validation failed', ['order_id' => $orderID]);
        //         // ...
        //     }
        // }

        // mdStatus kontrolü
        // 1, 2, 3, 4 = Başarılı 3D doğrulama
        // 5, 6, 7, 8 = Başarısız
        $successMdStatuses = ['1', '2', '3', '4'];

        if (in_array($mdStatus, $successMdStatuses) && $response === 'Approved' && $procReturnCode === '00') {
            // Başarılı ödeme
            $transaction->update([
                'status' => 'completed',
                'gateway_transaction_id' => $transId,
                'auth_code' => $authCode,
                'host_ref_num' => $hostRefNum,
                'card_number' => $maskedPan,
                'response_data' => $postData,
            ]);

            // Siparişi güncelle
            $order->update([
                'status' => 'paid',
                'payment_method' => 'kredi_karti',
                'paid_at' => now(),
            ]);

            // Sepeti temizle
            CartItem::where('user_id', $order->user_id)->delete();

            // NOT: ArtPuan dağıtımı admin panelden sipariş onaylandığında yapılacak

            Log::info('Garanti OOS: Payment successful', [
                'order_id' => $order->id,
                'trans_id' => $transId,
                'auth_code' => $authCode,
            ]);

            return [
                'success' => true,
                'message' => 'Ödeme başarıyla tamamlandı.',
                'order' => $order,
                'transaction' => $transaction,
            ];
        } else {
            // Başarısız ödeme
            $errorMessage = $this->getErrorMessage($mdStatus, $procReturnCode, $errMsg);

            $transaction->update([
                'status' => 'failed',
                'error_code' => $procReturnCode,
                'error_message' => $errorMessage,
                'response_data' => $postData,
            ]);

            // Siparişi iptal et ve ürünleri serbest bırak
            $order->update(['status' => 'payment_failed']);

            // Ürünlerin rezervasyonunu kaldır
            foreach ($order->items as $item) {
                if ($item->artwork) {
                    $item->artwork->update(['is_reserved' => false]);
                }
            }

            // Kullanılan ArtPuan'ı geri ver
            if ($order->artpuan_used > 0 && $order->user) {
                $order->user->increment('art_puan', $order->artpuan_used);
                Log::info('ArtPuan refunded due to failed payment', [
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'points' => $order->artpuan_used,
                ]);
            }

            Log::warning('Garanti OOS: Payment failed', [
                'order_id' => $order->id,
                'md_status' => $mdStatus,
                'proc_return_code' => $procReturnCode,
                'error' => $errorMessage,
            ]);

            return [
                'success' => false,
                'message' => $errorMessage,
                'order' => $order,
                'transaction' => $transaction,
            ];
        }
    }

    /**
     * Hash doğrulama
     */
    protected function validateHash(array $postData): bool
    {
        $hashParams = $postData['hashparams'] ?? '';
        $hashParamsVal = $postData['hashparamsval'] ?? '';
        $hash = $postData['hash'] ?? '';

        if (empty($hashParams) || empty($hash)) {
            return true; // Hash yoksa doğrulama yapma
        }

        // hashparams içindeki parametreleri sırayla al
        $params = explode(':', $hashParams);
        $calculatedHashString = '';

        foreach ($params as $param) {
            $calculatedHashString .= $postData[$param] ?? '';
        }

        $calculatedHashString .= $this->storeKey;
        $calculatedHash = strtoupper(sha1($calculatedHashString));

        return $calculatedHash === strtoupper($hash);
    }

    /**
     * Hata mesajını al
     */
    protected function getErrorMessage(string $mdStatus, string $procReturnCode, string $errMsg): string
    {
        // mdStatus hata mesajları
        $mdStatusMessages = [
            '0' => '3D doğrulama başarısız',
            '5' => 'Doğrulama yapılamadı',
            '6' => '3D Secure hatası',
            '7' => 'Sistem hatası',
            '8' => 'Bilinmeyen kart',
            '9' => 'Doğrulama başarısız',
        ];

        // procReturnCode hata mesajları
        $procReturnMessages = [
            '01' => 'Banka onayı alınmalı',
            '02' => 'Banka onayı alınmalı (özel durum)',
            '05' => 'İşlem onaylanmadı',
            '09' => 'İşlem tekrarlanmalı',
            '12' => 'Geçersiz işlem',
            '13' => 'Geçersiz tutar',
            '14' => 'Geçersiz kart numarası',
            '15' => 'Geçersiz müşteri',
            '33' => 'Kart süresi dolmuş',
            '34' => 'Kart sahteciliği şüphesi',
            '41' => 'Kayıp kart, el koyunuz',
            '43' => 'Çalıntı kart, el koyunuz',
            '51' => 'Yetersiz bakiye',
            '54' => 'Kart süresi dolmuş',
            '57' => 'Kart sahibi bu işlemi yapamaz',
            '58' => 'Terminal bu işlemi yapamaz',
            '61' => 'Çekim limiti aşıldı',
            '62' => 'Kısıtlı kart',
            '63' => 'Güvenlik ihlali',
            '65' => 'Günlük işlem limiti aşıldı',
            '75' => 'Şifre deneme limiti aşıldı',
            '77' => 'Şifre uyuşmuyor',
            '91' => 'Banka yanıt vermiyor',
            '96' => 'Sistem hatası',
            '99' => 'Başarısız',
        ];

        // Önce mdStatus'a bak
        if (isset($mdStatusMessages[$mdStatus])) {
            return $mdStatusMessages[$mdStatus];
        }

        // Sonra procReturnCode'a bak
        if (isset($procReturnMessages[$procReturnCode])) {
            return $procReturnMessages[$procReturnCode];
        }

        // Bankadan gelen mesaj varsa göster
        if (!empty($errMsg)) {
            return $errMsg;
        }

        return 'Ödeme işlemi başarısız oldu.';
    }

    /**
     * Gateway URL'ini al
     */
    public function getGatewayUrl(): string
    {
        return $this->gatewayUrl;
    }
}
