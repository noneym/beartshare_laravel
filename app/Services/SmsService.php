<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $username;
    protected string $password;
    protected string $header;
    protected string $apiUrl = 'https://api.netgsm.com.tr/sms/send/get';
    protected int $maxRetries = 2;

    public function __construct()
    {
        $this->username = config('services.netgsm.username', '');
        $this->password = config('services.netgsm.password', '');
        $this->header = config('services.netgsm.header', 'BEARTSHARE');
    }

    /**
     * SMS gonder - NetGSM HTTP GET API
     */
    public function send(string $phone, string $message): array
    {
        // Telefon numarasini temizle
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Basinda 0 varsa kaldir
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        // Basinda 90 yoksa ekle (Turkiye kodu)
        if (!str_starts_with($phone, '90') && strlen($phone) === 10) {
            $phone = '90' . $phone;
        }

        $params = http_build_query([
            'usercode'  => $this->username,
            'password'  => $this->password,
            'gsmno'     => $phone,
            'message'   => $message,
            'msgheader' => $this->header,
        ]);

        $url = $this->apiUrl . '?' . $params;

        $lastError = '';
        $response = '';
        $httpCode = 0;

        // Retry mekanizmasi
        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_CONNECTTIMEOUT => 15,
                    CURLOPT_DNS_CACHE_TIMEOUT => 300,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                Log::info("NetGSM Request (attempt {$attempt}) - Phone: {$phone}, Header: {$this->header}");
                Log::info("NetGSM Response (attempt {$attempt}) - HTTP: {$httpCode}, Body: {$response}");

                if ($curlError) {
                    $lastError = $curlError;
                    Log::warning("NetGSM cURL hatasi (attempt {$attempt}): {$curlError}");

                    if ($attempt < $this->maxRetries) {
                        sleep(2); // 2 saniye bekle ve tekrar dene
                        continue;
                    }

                    return [
                        'success' => false,
                        'error' => "cURL hatasi: {$curlError}",
                        'phone' => $phone,
                        'response' => $response,
                    ];
                }

                if ($httpCode !== 200) {
                    $lastError = "HTTP {$httpCode}";
                    Log::warning("NetGSM HTTP hatasi (attempt {$attempt}): {$httpCode}");

                    if ($attempt < $this->maxRetries) {
                        sleep(2);
                        continue;
                    }

                    return [
                        'success' => false,
                        'error' => "HTTP hatasi: {$httpCode}",
                        'phone' => $phone,
                        'response' => $response,
                    ];
                }

                $response = trim($response);

                // Basarili: 00 veya 01 veya 02 ile baslar
                if (str_starts_with($response, '00') || str_starts_with($response, '01') || str_starts_with($response, '02')) {
                    Log::info('SMS basariyla gonderildi: ' . $phone);
                    return [
                        'success' => true,
                        'phone' => $phone,
                        'response' => $response,
                    ];
                }

                // Hata kodlari
                $errorMessages = [
                    '20' => 'Mesaj metninde hata',
                    '30' => 'Gecersiz kullanici adi / sifre / baslik',
                    '40' => 'Mesaj baslik bilgisi bulunamadi',
                    '50' => 'Alici numarasi hatali',
                    '60' => 'Tanimli gonderim tarihi hatali',
                    '70' => 'Parametrelerde hata',
                ];

                $errorCode = substr($response, 0, 2);
                $errorMsg = $errorMessages[$errorCode] ?? "Bilinmeyen hata kodu: {$response}";
                Log::warning("NetGSM hata kodu: {$response} - {$errorMsg} - Phone: {$phone}");

                return [
                    'success' => false,
                    'error' => "NetGSM: {$errorMsg} (kod: {$errorCode})",
                    'phone' => $phone,
                    'response' => $response,
                ];

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::error("SMS gonderim hatasi (attempt {$attempt}): {$lastError}");

                if ($attempt < $this->maxRetries) {
                    sleep(2);
                    continue;
                }
            }
        }

        return [
            'success' => false,
            'error' => "Tum denemeler basarisiz: {$lastError}",
            'phone' => $phone,
            'response' => $response,
        ];
    }

    /**
     * Dogrulama kodu olustur
     */
    public function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Dogrulama SMS'i gonder
     */
    public function sendVerificationCode(string $phone, string $code): array
    {
        $message = "BeArtShare dogrulama kodunuz: {$code}. Bu kodu kimseyle paylasmayiniz.";
        return $this->send($phone, $message);
    }
}
