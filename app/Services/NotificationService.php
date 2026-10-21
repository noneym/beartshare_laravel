<?php

namespace App\Services;

use App\Support\NotificationTemplates;
use App\Jobs\NotifyAdminRecipients;
use App\Models\Artwork;
use App\Models\NotificationLog;
use App\Models\NotificationRecipient;
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

    protected function sendSmsAndLog(string $phone, string $message, string $type, ?int $orderId = null, ?int $userId = null, ?int $artworkId = null): void
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
                'artwork_id' => $artworkId,
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
                'artwork_id' => $artworkId,
                'user_id' => $userId,
            ]);
        }
    }

    protected function sendEmailAndLog(string $to, string $subject, string $htmlBody, string $type, ?int $orderId = null, ?int $userId = null, ?int $artworkId = null): bool
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
                'artwork_id' => $artworkId,
                'user_id' => $userId,
            ]);

            return true;
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
                'artwork_id' => $artworkId,
                'user_id' => $userId,
            ]);

            return false;
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
    public function sendAdminEmail(string $to, string $subject, string $htmlBody, ?int $userId = null): bool
    {
        return $this->sendEmailAndLog($to, $subject, $htmlBody, 'admin_email', null, $userId);
    }

    // ── Bildirim Methodlari ──

    /**
     * Favori eser bildirimi: eseri favorisine eklemiş üyelere (hariç tutulan hariç).
     * event: reserved | sold | price_changed. Rezerve / satıldı aynı üyeye aynı eser için bir kez gider.
     */
    public function notifyFavoriteWatchers(Artwork $artwork, int $excludeUserId = 0, string $event = 'reserved', array $extra = []): void
    {
        $type = 'favorite_' . $event;
        if (!isset(NotificationTemplates::DEFINITIONS[$type])) {
            return;
        }
        // Satılmış esere rezerve / fiyat bildirimi gitmez
        if ($event !== 'sold' && $artwork->is_sold) {
            return;
        }

        $artwork->loadMissing('artist');
        $watchers = $artwork->favoritedBy()->where('users.id', '!=', $excludeUserId)->get();

        foreach ($watchers as $watcher) {
            if ($event !== 'price_changed') {
                $alreadyNotified = NotificationLog::where('type', $type)
                    ->where('user_id', $watcher->id)
                    ->where('artwork_id', $artwork->id)
                    ->where('status', 'success')
                    ->exists();
                if ($alreadyNotified) {
                    continue;
                }
            }

            [$first, $last] = NotificationTemplates::nameParts($watcher->name);
            $msg = NotificationTemplates::render($type, array_merge([
                'FIRSTNAME' => $first,
                'LASTNAME' => $last,
                'ARTWORK_TITLE' => $artwork->title,
                'ARTIST_NAME' => $artwork->artist->name ?? '',
                'ARTWORK_URL' => route('artwork.detail', $artwork->slug),
                'ARTWORKS_URL' => route('artworks'),
            ], $extra));

            if ($msg['sms'] && $watcher->phone) {
                $this->sendSmsAndLog($watcher->phone, $msg['sms'], $type, null, $watcher->id, $artwork->id);
            }
            if ($msg['html'] && $watcher->email) {
                $this->sendEmailAndLog($watcher->email, $msg['subject'], $msg['html'], $type, null, $watcher->id, $artwork->id);
            }
        }
    }

    /**
     * Sipariş onaylandığında alıcıya bildirim (kazanılan ArtPuan ile)
     */
    public function notifyBuyerArtPuan(User $buyer, float $puanAmount, Order $order): void
    {
        [$first, $last] = NotificationTemplates::nameParts($buyer->name);
        $msg = NotificationTemplates::render('order_confirmed', [
            'FIRSTNAME' => $first,
            'LASTNAME' => $last,
            'ORDER_NUMBER' => $order->order_number,
            'ORDER_ID' => $order->id,
            'ARTPUAN' => $this->puan($puanAmount),
            'ARTPUAN_BALANCE' => $this->puan($buyer->art_puan),
        ]);

        if ($msg['sms'] && $buyer->phone) {
            $this->sendSmsAndLog($buyer->phone, $msg['sms'], 'buyer_artpuan', $order->id, $buyer->id);
        }
        if ($msg['html'] && $buyer->email) {
            $this->sendEmailAndLog($buyer->email, $msg['subject'], $msg['html'], 'buyer_artpuan', $order->id, $buyer->id);
        }
    }

    /**
     * Sipariş onaylandığında referans sahibine ArtPuan bildirimi
     */
    public function notifyReferrerArtPuan(User $referrer, User $buyer, float $puanAmount, Order $order): void
    {
        [$first, $last] = NotificationTemplates::nameParts($referrer->name);
        $msg = NotificationTemplates::render('referrer_artpuan', [
            'FIRSTNAME' => $first,
            'LASTNAME' => $last,
            'BUYER_FIRSTNAME' => NotificationTemplates::nameParts($buyer->name)[0],
            'ARTPUAN' => $this->puan($puanAmount),
            'ARTPUAN_BALANCE' => $this->puan($referrer->art_puan),
            'ORDER_NUMBER' => $order->order_number,
        ]);

        if ($msg['sms'] && $referrer->phone) {
            $this->sendSmsAndLog($referrer->phone, $msg['sms'], 'referrer_artpuan', $order->id, $referrer->id);
        }
        if ($msg['html'] && $referrer->email) {
            $this->sendEmailAndLog($referrer->email, $msg['subject'], $msg['html'], 'referrer_artpuan', $order->id, $referrer->id);
        }
    }

    /** ArtPuan miktarı: küsurat yoksa tam sayı (650), varsa iki hane (650,50) */
    protected function puan(float $amount): string
    {
        return number_format($amount, fmod($amount, 1.0) == 0.0 ? 0 : 2, ',', '.');
    }

    /**
     * Sipariş oluşturulduğunda alıcıya onay bildirimi
     */
    public function notifyOrderCreated(Order $order): void
    {
        $isTransfer = $order->payment_method === 'havale';
        $totalTl = number_format($order->total_tl, 0, ',', '.');
        $bankPageUrl = url('/banka-hesaplari');
        [$first, $last] = NotificationTemplates::nameParts($order->customer_name);

        $msg = NotificationTemplates::render('order_created', [
            'FIRSTNAME' => $first,
            'LASTNAME' => $last,
            'ORDER_NUMBER' => $order->order_number,
            'ORDER_ID' => $order->id,
            'TOTAL' => $totalTl,
            'PAYMENT_CODE' => $order->payment_code,
            'BANK_URL' => $bankPageUrl,
            'BANK_TRANSFER_SMS' => $isTransfer
                ? "Ödeme tutarı: {$totalTl} TL. Havale açıklama kodunuz: {$order->payment_code}. Banka hesaplarımız: {$bankPageUrl} Eserler 30 dk rezervedir. Dekont için: info@beartshare.com"
                : '',
            'PAYMENT_INFO_HTML' => $isTransfer ? $this->buildOrderPaymentInfoHtml($order) : $this->buildArtPuanUsedHtml($order),
            'ORDER_ITEMS_HTML' => $this->buildOrderItemsHtml($order),
        ]);

        $userId = $order->user_id;
        if ($msg['sms'] && $order->customer_phone) {
            $this->sendSmsAndLog($order->customer_phone, $msg['sms'], 'order_created', $order->id, $userId);
        }
        if ($msg['html'] && $order->customer_email) {
            $this->sendEmailAndLog($order->customer_email, $msg['subject'], $msg['html'], 'order_created', $order->id, $userId);
        }

        $this->notifyAdminsNewOrder($order);
    }

    /**
     * Yeni sipariş → Bildirim Alıcıları (new_order). Havalede sipariş oluşunca,
     * kartta ödeme başarılı olunca çağrılır (notifyOrderCreated üzerinden).
     */
    public function notifyAdminsNewOrder(Order $order): void
    {
        $order->loadMissing('items');
        $total = number_format($order->total_tl, 0, ',', '.') . ' TL';
        $method = $order->payment_method_label;
        $adminUrl = route('admin.orders.show', $order->id);
        $titles = $order->items->pluck('artwork_title')->filter()->implode(', ');

        $sms = NotificationTemplates::render('admin_new_order', [
            'ORDER_NUMBER' => $order->order_number,
            'ORDER_ID' => $order->id,
            'CUSTOMER_NAME' => $order->customer_name,
            'TOTAL' => number_format($order->total_tl, 0, ',', '.'),
            'PAYMENT_METHOD' => $method,
            'ARTWORK_TITLES' => \Illuminate\Support\Str::limit($titles, 60),
        ])['sms'] ?? '';

        $rows = '';
        foreach ($order->items as $item) {
            $rows .= "<tr><td style='padding:6px 0;border-bottom:1px solid #eee;'>" . e($item->artwork_title) . "<br><span style='color:#888;font-size:12px;'>" . e($item->artist_name) . "</span></td>"
                . "<td style='padding:6px 0;border-bottom:1px solid #eee;text-align:right;white-space:nowrap;'>" . number_format($item->price_tl, 0, ',', '.') . " TL</td></tr>";
        }
        $artpuan = $order->artpuan_used > 0
            ? "<tr><td style='padding:6px 0;color:#888;'>ArtPuan kullanıldı</td><td style='padding:6px 0;text-align:right;'>-" . number_format($order->artpuan_used, 0, ',', '.') . " TL</td></tr>"
            : '';

        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #14171c; padding: 20px 24px;'>
                <h1 style='color: #fff; font-size: 18px; margin: 0;'>Yeni Sipariş · " . e($order->order_number) . "</h1>
            </div>
            <div style='padding: 24px; background: #fff; border: 1px solid #eee; font-size: 14px; color: #333;'>
                <table style='width:100%;'>
                    <tr><td style='padding:4px 0;width:120px;color:#888;'>Müşteri</td><td>" . e($order->customer_name) . "</td></tr>
                    <tr><td style='padding:4px 0;color:#888;'>Telefon</td><td>" . e($order->customer_phone ?: '-') . "</td></tr>
                    <tr><td style='padding:4px 0;color:#888;'>E-posta</td><td>" . e($order->customer_email) . "</td></tr>
                    <tr><td style='padding:4px 0;color:#888;'>Ödeme</td><td>" . e($method) . ($order->payment_code ? ' · Kod: ' . e($order->payment_code) : '') . "</td></tr>
                    <tr><td style='padding:4px 0;color:#888;'>Adres</td><td>" . e(trim($order->shipping_address . ' ' . $order->district . '/' . $order->city, ' /')) . "</td></tr>
                </table>
                <table style='width:100%;margin-top:16px;'>{$rows}{$artpuan}
                    <tr><td style='padding:8px 0;font-weight:bold;'>Toplam</td><td style='padding:8px 0;text-align:right;font-weight:bold;'>{$total}</td></tr>
                </table>
                " . ($order->notes ? "<p style='background:#f8f8f8;padding:12px;margin:16px 0 0;'><b>Not:</b> " . e($order->notes) . "</p>" : '') . "
                <p style='margin:24px 0 0;'><a href='" . e($adminUrl) . "' style='display:inline-block;background:#D4A017;color:#fff;padding:10px 18px;text-decoration:none;'>Siparişi admin panelde aç</a></p>
            </div>
        </div>";

        $this->notifyRecipients('new_order', "Yeni Sipariş: {$order->order_number} - {$total}", $html, $sms, $order->id, $order->customer_email, $order->customer_name);
    }

    /**
     * Site formlarından gelen bildirimi yöneticiye (config mail.admin_address) gönderir.
     * Yanıtla → formu dolduran kişiye gider. Sonuç Bildirim Log'a yazılır.
     */
    public function sendAdminNotification(string $subject, string $htmlBody, string $type, ?string $replyTo = null, ?string $replyName = null, ?int $userId = null): bool
    {
        $to = config('mail.admin_address', 'info@beartshare.com');
        $sent = $this->sendRecipientEmail($to, $subject, $htmlBody, $type, null, $replyTo, $replyName, $userId);

        // Ayrıca Admin > Bildirim Alıcıları'nda bu olayı seçenlere (kuyruktan)
        if (isset(NotificationRecipient::EVENTS[$type])) {
            $this->notifyRecipients($type, $subject, $htmlBody, 'BeArtShare: ' . $subject, null, $replyTo, $replyName, $to);
        }

        return $sent;
    }

    /**
     * Olayı Bildirim Alıcıları listesindeki kişilere kuyruk üzerinden gönderir.
     */
    public function notifyRecipients(string $event, string $subject, string $htmlBody, string $smsText, ?int $orderId = null, ?string $replyTo = null, ?string $replyName = null, ?string $skipEmail = null): void
    {
        try {
            NotifyAdminRecipients::dispatch($event, $subject, $htmlBody, $smsText, $orderId, $replyTo, $replyName, $skipEmail);
        } catch (\Throwable $e) {
            Log::error("Bildirim alicilari kuyruga alinamadi ({$event}): " . $e->getMessage());
        }
    }

    /**
     * Tek bir yönetici/alıcıya e-posta (Yanıtla → formu dolduran kişi). Sonuç Bildirim Log'a yazılır.
     */
    public function sendRecipientEmail(string $to, string $subject, string $htmlBody, string $type, ?int $orderId = null, ?string $replyTo = null, ?string $replyName = null, ?int $userId = null): bool
    {
        try {
            Mail::html($htmlBody, function ($message) use ($to, $subject, $replyTo, $replyName) {
                $message->to($to)
                    ->subject($subject)
                    ->from(config('mail.from.address', 'info@beartshare.com'), 'BeArtShare');
                if ($replyTo) {
                    $message->replyTo($replyTo, $replyName);
                }
            });
            $status = 'success';
            $error = null;
        } catch (\Throwable $e) {
            Log::error("Yonetici bildirimi gonderilemedi ({$type}): " . $e->getMessage());
            $status = 'failed';
            $error = $e->getMessage();
        }

        $this->logNotification([
            'channel' => 'email',
            'type' => $type,
            'recipient' => $to,
            'subject' => $subject,
            'message' => mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($htmlBody))), 0, 500),
            'status' => $status,
            'error' => $error,
            'order_id' => $orderId,
            'user_id' => $userId,
        ]);

        return $status === 'success';
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

    /**
     * Şifre sıfırlama bağlantısı e-postası (şablon kapatılamaz: kapalıysa da gönderilir)
     */
    public function sendPasswordReset(User $user, string $url): bool
    {
        [$first, $last] = NotificationTemplates::nameParts($user->name);
        $vars = [
            'FIRSTNAME' => $first,
            'LASTNAME' => $last,
            'RESET_URL' => $url,
            'MINUTES' => config('auth.passwords.users.expire', 60),
        ];
        $t = NotificationTemplates::get('password_reset');
        $html = NotificationTemplates::layout(NotificationTemplates::fill($t['email_body'], $vars, true));
        $subject = NotificationTemplates::fill($t['email_subject'], $vars, false);

        return $this->sendEmailAndLog($user->email, $subject, $html, 'password_reset', null, $user->id);
    }

    // ── E-posta Şablonları ──

    /**
     * Manuel ArtPuan ekleme bildirimi
     */
    public function notifyManualArtPuan(User $user, float $puanAmount, string $description, array $channels = ['sms', 'email']): void
    {
        [$first, $last] = NotificationTemplates::nameParts($user->name);
        $msg = NotificationTemplates::render('manual_artpuan', [
            'FIRSTNAME' => $first,
            'LASTNAME' => $last,
            'ARTPUAN' => $this->puan($puanAmount),
            'DESCRIPTION' => $description,
            'ARTPUAN_BALANCE' => $this->puan($user->art_puan),
        ]);

        if (in_array('sms', $channels) && $msg['sms'] && $user->phone) {
            $this->sendSmsAndLog($user->phone, $msg['sms'], 'manual_artpuan', null, $user->id);
        }
        if (in_array('email', $channels) && $msg['html'] && $user->email) {
            $this->sendEmailAndLog($user->email, $msg['subject'], $msg['html'], 'manual_artpuan', null, $user->id);
        }
    }

    /** Sipariş e-postası: eser listesi ve toplam */
    protected function buildOrderItemsHtml(Order $order): string
    {
        $order->loadMissing('items.artwork');
        $itemsHtml = '';
        foreach ($order->items as $item) {
            $price = number_format($item->price_tl, 0, ',', '.');
            $itemsHtml .= "
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; font-size: 13px;'>" . e($item->artwork_title) . "</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; font-size: 13px;'>" . e($item->artist_name) . "</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; font-size: 13px; text-align: right;'>{$price} TL</td>
                </tr>";
        }
        $totalTl = number_format($order->total_tl, 0, ',', '.');

        return "
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
                </table>";
    }

    /** Sipariş e-postası: kullanılan ArtPuan bilgisi (yoksa boş) */
    protected function buildArtPuanUsedHtml(Order $order): string
    {
        if ($order->artpuan_used <= 0) {
            return '';
        }
        $totalTl = number_format($order->total_tl, 0, ',', '.');
        $subtotalTl = number_format($order->total_tl + ($order->discount_tl ?? 0), 0, ',', '.');
        $discountTl = number_format($order->discount_tl, 0, ',', '.');
        $artpuanUsed = number_format($order->artpuan_used, 2, ',', '.');

        return "
                <div style='background: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px 16px; margin: 12px 0; border-radius: 8px;'>
                    <p style='color: #15803d; font-size: 13px; margin: 0;'>
                        &#11088; <strong>{$artpuanUsed} ArtPuan</strong> kullanıldı, <strong>{$discountTl} TL</strong> indirim uygulandı.
                    </p>
                    <p style='color: #166534; font-size: 12px; margin: 4px 0 0;'>Ara Toplam: {$subtotalTl} TL &rarr; Ödenecek: <strong>{$totalTl} TL</strong></p>
                </div>";
    }

    /** Sipariş e-postası (havale): ödeme kodu, banka hesapları, uyarılar */
    protected function buildOrderPaymentInfoHtml(Order $order): string
    {
        $bankPageUrl = url('/banka-hesaplari');
        $artpuanHtml = $this->buildArtPuanUsedHtml($order);

        return "
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
}
