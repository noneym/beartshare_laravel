<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    protected SmsService $smsService;

    public function __construct()
    {
        $this->smsService = new SmsService();
    }

    // ── Log Helper ──

    protected function logNotification(array $data): NotificationLog
    {
        return NotificationLog::create($data);
    }

    protected function sendSmsAndLog(string $phone, string $message, string $type, ?int $orderId = null, ?int $userId = null): void
    {
        try {
            $result = $this->smsService->send($phone, $message);

            $this->logNotification([
                'channel' => 'sms',
                'type' => $type,
                'recipient' => $phone,
                'message' => mb_substr($message, 0, 500),
                'status' => $result['success'] ? 'success' : 'failed',
                'error' => $result['error'] ?? null,
                'api_response' => $result['response'] ?? null,
                'order_id' => $orderId,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error("SMS gonderim hatasi ({$type}): " . $e->getMessage());

            $this->logNotification([
                'channel' => 'sms',
                'type' => $type,
                'recipient' => $phone,
                'message' => mb_substr($message, 0, 500),
                'status' => 'failed',
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'user_id' => $userId,
            ]);
        }
    }

    protected function sendEmailAndLog(string $to, string $subject, string $htmlBody, string $type, ?int $orderId = null, ?int $userId = null): void
    {
        try {
            $this->sendEmail($to, $subject, $htmlBody);

            $this->logNotification([
                'channel' => 'email',
                'type' => $type,
                'recipient' => $to,
                'subject' => $subject,
                'message' => mb_substr(strip_tags($htmlBody), 0, 500),
                'status' => 'success',
                'order_id' => $orderId,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error("E-posta gonderim hatasi ({$type}): " . $e->getMessage());

            $this->logNotification([
                'channel' => 'email',
                'type' => $type,
                'recipient' => $to,
                'subject' => $subject,
                'message' => mb_substr(strip_tags($htmlBody), 0, 500),
                'status' => 'failed',
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'user_id' => $userId,
            ]);
        }
    }

    /**
     * Genel amaçlı SMS gönderip logla (dışarıdan erişilebilir)
     */
    public function sendSmsWithLog(string $phone, string $message, string $type, ?int $orderId = null, ?int $userId = null): array
    {
        try {
            $result = $this->smsService->send($phone, $message);

            $this->logNotification([
                'channel' => 'sms',
                'type' => $type,
                'recipient' => $phone,
                'message' => mb_substr($message, 0, 500),
                'status' => $result['success'] ? 'success' : 'failed',
                'error' => $result['error'] ?? null,
                'api_response' => $result['response'] ?? null,
                'order_id' => $orderId,
                'user_id' => $userId,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error("SMS gonderim hatasi ({$type}): " . $e->getMessage());

            $this->logNotification([
                'channel' => 'sms',
                'type' => $type,
                'recipient' => $phone,
                'message' => mb_substr($message, 0, 500),
                'status' => 'failed',
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'user_id' => $userId,
            ]);

            return ['success' => false, 'error' => $e->getMessage(), 'phone' => $phone, 'response' => ''];
        }
    }

    /**
     * Admin panelden e-posta gonder ve logla (public)
     */
    public function sendAdminEmail(string $to, string $subject, string $htmlBody, ?int $userId = null): void
    {
        $this->sendEmailAndLog($to, $subject, $htmlBody, 'admin_email', null, $userId);
    }

    // ── Bildirim Methodlari ──

    /**
     * Eser rezerve edildiğinde favorisine eklemiş kullanıcılara bildirim gönder
     */
    public function notifyFavoriteWatchers(Artwork $artwork, int $excludeUserId = 0): void
    {
        $watchers = $artwork->favoritedBy()
            ->where('users.id', '!=', $excludeUserId)
            ->get();

        foreach ($watchers as $watcher) {
            $message = "BeArtShare: Favori listenizde bulunan \"{$artwork->title}\" eseri rezerve edildi.";

            if ($watcher->phone) {
                $this->sendSmsAndLog($watcher->phone, $message, 'favorite_reserved', null, $watcher->id);
            }

            if ($watcher->email) {
                $this->sendEmailAndLog(
                    $watcher->email,
                    'Favori Eseriniz Rezerve Edildi',
                    $this->buildReservedEmailBody($artwork, $watcher),
                    'favorite_reserved',
                    null,
                    $watcher->id
                );
            }
        }
    }

    /**
     * Sipariş onaylandığında ArtPuan bildirimi gönder (alıcıya)
     */
    public function notifyBuyerArtPuan(User $buyer, float $puanAmount, Order $order): void
    {
        $formattedPuan = number_format($puanAmount, 2, ',', '.');
        $message = "BeArtShare: Siparisiniiz onaylanmistir. {$formattedPuan} ArtPuan kazandiniz! Siparis No: {$order->order_number}";

        if ($buyer->phone) {
            $this->sendSmsAndLog($buyer->phone, $message, 'buyer_artpuan', $order->id, $buyer->id);
        }

        if ($buyer->email) {
            $this->sendEmailAndLog(
                $buyer->email,
                'Siparişiniz Onaylandı - ArtPuan Kazandınız!',
                $this->buildBuyerArtPuanEmailBody($buyer, $puanAmount, $order),
                'buyer_artpuan',
                $order->id,
                $buyer->id
            );
        }
    }

    /**
     * Sipariş onaylandığında referans sahibine ArtPuan bildirimi gönder
     */
    public function notifyReferrerArtPuan(User $referrer, User $buyer, float $puanAmount, Order $order): void
    {
        $formattedPuan = number_format($puanAmount, 2, ',', '.');
        $buyerFirstName = explode(' ', $buyer->name)[0];

        $message = "BeArtShare: Referansiniz {$buyerFirstName} bir satin alma yapti. {$formattedPuan} ArtPuan kazandiniz!";

        if ($referrer->phone) {
            $this->sendSmsAndLog($referrer->phone, $message, 'referrer_artpuan', $order->id, $referrer->id);
        }

        if ($referrer->email) {
            $this->sendEmailAndLog(
                $referrer->email,
                'Referansınızdan ArtPuan Kazandınız!',
                $this->buildReferrerArtPuanEmailBody($referrer, $buyer, $puanAmount, $order),
                'referrer_artpuan',
                $order->id,
                $referrer->id
            );
        }
    }

    /**
     * Sipariş oluşturulduğunda alıcıya onay bildirimi
     */
    public function notifyOrderCreated(Order $order): void
    {
        $bankPageUrl = url('/banka-hesaplari');

        $message = "BeArtShare: Siparisiniiz basariyla olusturuldu. Siparis No: {$order->order_number}. ";
        if ($order->payment_method === 'havale') {
            $totalTl = number_format($order->total_tl, 0, ',', '.');
            $message .= "Odeme tutari: {$totalTl} TL. Havale aciklama kodunuz: {$order->payment_code}. ";
            $message .= "Banka hesaplarimiz: {$bankPageUrl} ";
            $message .= "Eserler 30 dk rezervedir. Dekont icin: info@beartshare.com";
        }

        $userId = $order->user_id;

        if ($order->customer_phone) {
            $this->sendSmsAndLog($order->customer_phone, $message, 'order_created', $order->id, $userId);
        }

        if ($order->customer_email) {
            $this->sendEmailAndLog(
                $order->customer_email,
                'Siparişiniz Alındı - ' . $order->order_number,
                $this->buildOrderCreatedEmailBody($order),
                'order_created',
                $order->id,
                $userId
            );
        }
    }

    /**
     * Basit HTML e-posta gönder
     */
    protected function sendEmail(string $to, string $subject, string $htmlBody): void
    {
        Mail::html($htmlBody, function ($message) use ($to, $subject) {
            $message->to($to)
                ->subject($subject)
                ->from(config('mail.from.address', 'info@beartshare.com'), 'BeArtShare');
        });
    }

    // ── E-posta Şablonları ──

    protected function buildReservedEmailBody(Artwork $artwork, User $watcher): string
    {
        $artworkUrl = route('artwork.detail', $artwork->slug);
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #1a1a1a; padding: 24px; text-align: center;'>
                <h1 style='color: #fff; font-size: 20px; margin: 0;'>BeArtShare</h1>
            </div>
            <div style='padding: 32px 24px; background: #fff;'>
                <p style='color: #333; font-size: 14px;'>Merhaba {$watcher->name},</p>
                <p style='color: #555; font-size: 14px;'>
                    Favori listenizde bulunan <strong>\"{$artwork->title}\"</strong> adlı eser rezerve edilmiştir.
                </p>
                <p style='color: #555; font-size: 14px;'>
                    Koleksiyonumuzdaki diğer eserleri incelemek için sitemizi ziyaret edebilirsiniz.
                </p>
                <div style='text-align: center; margin: 24px 0;'>
                    <a href='" . url('/eserler') . "' style='background: #1a1a1a; color: #fff; padding: 12px 32px; text-decoration: none; font-size: 14px;'>Eserleri Keşfet</a>
                </div>
            </div>
            <div style='padding: 16px 24px; background: #f8f8f8; text-align: center;'>
                <p style='color: #999; font-size: 11px; margin: 0;'>BeArtShare &copy; " . date('Y') . "</p>
            </div>
        </div>";
    }

    protected function buildBuyerArtPuanEmailBody(User $buyer, float $puanAmount, Order $order): string
    {
        $formattedPuan = number_format($puanAmount, 2, ',', '.');
        $totalPuan = number_format($buyer->art_puan, 2, ',', '.');
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #1a1a1a; padding: 24px; text-align: center;'>
                <h1 style='color: #fff; font-size: 20px; margin: 0;'>BeArtShare</h1>
            </div>
            <div style='padding: 32px 24px; background: #fff;'>
                <p style='color: #333; font-size: 14px;'>Merhaba {$buyer->name},</p>
                <p style='color: #555; font-size: 14px;'>
                    <strong>{$order->order_number}</strong> numaralı siparişiniz onaylanmıştır.
                </p>
                <div style='background: #f0fdf4; border: 1px solid #bbf7d0; padding: 16px; margin: 16px 0; text-align: center;'>
                    <p style='color: #16a34a; font-size: 24px; font-weight: bold; margin: 0;'>{$formattedPuan} ArtPuan</p>
                    <p style='color: #15803d; font-size: 12px; margin: 4px 0 0;'>kazandınız!</p>
                </div>
                <p style='color: #555; font-size: 14px;'>
                    Toplam ArtPuan bakiyeniz: <strong>{$totalPuan} AP</strong>
                </p>
            </div>
            <div style='padding: 16px 24px; background: #f8f8f8; text-align: center;'>
                <p style='color: #999; font-size: 11px; margin: 0;'>BeArtShare &copy; " . date('Y') . "</p>
            </div>
        </div>";
    }

    protected function buildReferrerArtPuanEmailBody(User $referrer, User $buyer, float $puanAmount, Order $order): string
    {
        $formattedPuan = number_format($puanAmount, 2, ',', '.');
        $totalPuan = number_format($referrer->art_puan, 2, ',', '.');
        $buyerFirstName = explode(' ', $buyer->name)[0];

        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #1a1a1a; padding: 24px; text-align: center;'>
                <h1 style='color: #fff; font-size: 20px; margin: 0;'>BeArtShare</h1>
            </div>
            <div style='padding: 32px 24px; background: #fff;'>
                <p style='color: #333; font-size: 14px;'>Merhaba {$referrer->name},</p>
                <p style='color: #555; font-size: 14px;'>
                    Referansınız <strong>{$buyerFirstName}</strong> bir satın alma gerçekleştirdi.
                </p>
                <div style='background: #f0fdf4; border: 1px solid #bbf7d0; padding: 16px; margin: 16px 0; text-align: center;'>
                    <p style='color: #16a34a; font-size: 24px; font-weight: bold; margin: 0;'>{$formattedPuan} ArtPuan</p>
                    <p style='color: #15803d; font-size: 12px; margin: 4px 0 0;'>kazandınız!</p>
                </div>
                <p style='color: #555; font-size: 14px;'>
                    Toplam ArtPuan bakiyeniz: <strong>{$totalPuan} AP</strong>
                </p>
            </div>
            <div style='padding: 16px 24px; background: #f8f8f8; text-align: center;'>
                <p style='color: #999; font-size: 11px; margin: 0;'>BeArtShare &copy; " . date('Y') . "</p>
            </div>
        </div>";
    }

    protected function buildOrderCreatedEmailBody(Order $order): string
    {
        $order->load('items.artwork');
        $itemsHtml = '';
        foreach ($order->items as $item) {
            $price = number_format($item->price_tl, 0, ',', '.');
            $itemsHtml .= "
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; font-size: 13px;'>{$item->artwork_title}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; font-size: 13px;'>{$item->artist_name}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; font-size: 13px; text-align: right;'>{$price} TL</td>
                </tr>";
        }

        $totalTl = number_format($order->total_tl, 0, ',', '.');
        $bankPageUrl = url('/banka-hesaplari');

        // ArtPuan indirim bilgisi
        $artpuanHtml = '';
        if ($order->artpuan_used > 0) {
            $subtotalTl = number_format($order->total_tl + ($order->discount_tl ?? 0), 0, ',', '.');
            $discountTl = number_format($order->discount_tl, 0, ',', '.');
            $artpuanUsed = number_format($order->artpuan_used, 2, ',', '.');
            $artpuanHtml = "
                <div style='background: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px 16px; margin: 12px 0; border-radius: 8px;'>
                    <p style='color: #15803d; font-size: 13px; margin: 0;'>
                        &#11088; <strong>{$artpuanUsed} ArtPuan</strong> kullanıldı, <strong>{$discountTl} TL</strong> indirim uygulandı.
                    </p>
                    <p style='color: #166534; font-size: 12px; margin: 4px 0 0;'>Ara Toplam: {$subtotalTl} TL &rarr; Ödenecek: <strong>{$totalTl} TL</strong></p>
                </div>";
        }

        $paymentInfo = '';
        if ($order->payment_method === 'havale') {
            $paymentInfo = "
                <!-- Ödeme Kodu -->
                <div style='background: #fefce8; border: 2px solid #fde68a; padding: 20px; margin: 20px 0; text-align: center; border-radius: 8px;'>
                    <p style='color: #92400e; font-size: 12px; margin: 0 0 4px;'>Havale Açıklama Kodunuz</p>
                    <p style='color: #78350f; font-size: 28px; font-weight: bold; font-family: monospace; margin: 0; letter-spacing: 2px;'>{$order->payment_code}</p>
                    <p style='color: #a16207; font-size: 11px; margin: 4px 0 0;'>Bu kodu havale/EFT açıklamasına mutlaka yazınız</p>
                </div>

                {$artpuanHtml}

                <!-- Banka Hesapları -->
                <div style='margin: 20px 0;'>
                    <p style='color: #333; font-size: 14px; font-weight: bold; margin: 0 0 4px;'>Banka Hesaplarımız</p>
                    <p style='color: #888; font-size: 11px; margin: 0 0 12px;'>Hesap Sahibi: BEARTSHARE ONLİNE SANAT GALERİSİ ANONİM ŞİRKETİ</p>

                    <!-- Garanti Bankası -->
                    <div style='background: #f0fdf4; border: 1px solid #dcfce7; padding: 14px 16px; margin: 0 0 8px; border-radius: 8px;'>
                        <p style='color: #166534; font-size: 13px; font-weight: bold; margin: 0 0 6px;'>&#127970; Garanti Bankası - Galatasaray Şubesi</p>
                        <p style='color: #555; font-size: 12px; margin: 0 0 2px;'>Hesap No: <strong>068-6291752</strong></p>
                        <p style='color: #555; font-size: 12px; margin: 0;'>IBAN: <strong style='font-family: monospace; letter-spacing: 1px;'>TR62 0006 2000 0680 0006 2917 52</strong></p>
                    </div>

                    <!-- Vakıflar Bankası -->
                    <div style='background: #eff6ff; border: 1px solid #dbeafe; padding: 14px 16px; border-radius: 8px;'>
                        <p style='color: #1e3a5f; font-size: 13px; font-weight: bold; margin: 0 0 6px;'>&#127970; Vakıflar Bankası - Levent Şubesi</p>
                        <p style='color: #555; font-size: 12px; margin: 0;'>IBAN: <strong style='font-family: monospace; letter-spacing: 1px;'>TR68 0001 5001 5800 7321 4175 83</strong></p>
                    </div>

                    <p style='text-align: center; margin: 12px 0 0;'>
                        <a href='{$bankPageUrl}' style='color: #d97706; font-size: 12px;'>Tüm banka hesap bilgilerimizi görüntüleyin &rarr;</a>
                    </p>
                </div>

                <!-- Uyarılar -->
                <div style='background: #fef2f2; border: 1px solid #fecaca; padding: 14px 16px; margin: 16px 0; border-radius: 8px;'>
                    <p style='color: #991b1b; font-size: 13px; font-weight: bold; margin: 0 0 4px;'>&#9200; 30 Dakika Rezervasyon Süresi</p>
                    <p style='color: #b91c1c; font-size: 12px; margin: 0;'>Seçtiğiniz eserler sipariş oluşturulduktan itibaren 30 dakika boyunca sizin için rezerve edilir. Bu süre içinde ödemenizi gerçekleştirmeniz gerekmektedir.</p>
                </div>

                <div style='background: #eff6ff; border: 1px solid #bfdbfe; padding: 14px 16px; margin: 0 0 16px; border-radius: 8px;'>
                    <p style='color: #1e40af; font-size: 13px; font-weight: bold; margin: 0 0 4px;'>&#128231; Ödeme Dekontu</p>
                    <p style='color: #1d4ed8; font-size: 12px; margin: 0;'>Ödemenizi yaptıktan sonra dekontunuzu <a href='mailto:info@beartshare.com' style='color: #1d4ed8; font-weight: bold;'>info@beartshare.com</a> adresine gönderebilirsiniz.</p>
                </div>";
        }

        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #1a1a1a; padding: 24px; text-align: center;'>
                <h1 style='color: #fff; font-size: 20px; margin: 0;'>BeArtShare</h1>
            </div>
            <div style='padding: 32px 24px; background: #fff;'>
                <p style='color: #333; font-size: 14px;'>Merhaba {$order->customer_name},</p>
                <p style='color: #555; font-size: 14px;'>
                    <strong>{$order->order_number}</strong> numaralı siparişiniz başarıyla oluşturulmuştur. Teşekkür ederiz!
                </p>

                {$paymentInfo}

                <table style='width: 100%; border-collapse: collapse; margin: 16px 0;'>
                    <thead>
                        <tr style='background: #f8f8f8;'>
                            <th style='padding: 8px; text-align: left; font-size: 12px; color: #666;'>Eser</th>
                            <th style='padding: 8px; text-align: left; font-size: 12px; color: #666;'>Sanatçı</th>
                            <th style='padding: 8px; text-align: right; font-size: 12px; color: #666;'>Fiyat</th>
                        </tr>
                    </thead>
                    <tbody>{$itemsHtml}</tbody>
                    <tfoot>
                        <tr>
                            <td colspan='2' style='padding: 12px 8px; text-align: right; font-weight: bold; font-size: 14px;'>Toplam:</td>
                            <td style='padding: 12px 8px; text-align: right; font-weight: bold; font-size: 14px;'>{$totalTl} TL</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div style='padding: 16px 24px; background: #f8f8f8; text-align: center;'>
                <p style='color: #999; font-size: 11px; margin: 0;'>BeArtShare &copy; " . date('Y') . "</p>
            </div>
        </div>";
    }
}
