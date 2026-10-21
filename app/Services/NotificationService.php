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

    /** Sipariş e-postası: eser listesi ve toplam (sınıflar NotificationTemplates::layout'ta stile çevrilir) */
    protected function buildOrderItemsHtml(Order $order): string
    {
        $order->loadMissing('items.artwork');
        $rows = '';
        foreach ($order->items as $item) {
            $price = number_format($item->price_tl, 0, ',', '.');
            $rows .= "<tr><td style='padding:8px 0;border-bottom:1px solid #e6e1d8;'><strong>" . e($item->artwork_title) . "</strong><br><span class=\"muted\">" . e($item->artist_name) . "</span></td>"
                . "<td style='padding:8px 0;border-bottom:1px solid #e6e1d8;text-align:right;white-space:nowrap;vertical-align:top;'>{$price} TL</td></tr>";
        }
        $totalTl = number_format($order->total_tl, 0, ',', '.');

        return "<div class=\"box\"><table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='font-size:14px;'>{$rows}"
            . "<tr><td style='padding:10px 0 0;'><strong>Toplam</strong></td><td style='padding:10px 0 0;text-align:right;white-space:nowrap;'><strong>{$totalTl} TL</strong></td></tr></table></div>";
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

        return "<p><strong>{$artpuanUsed} ArtPuan®</strong> kullanıldı, <strong>{$discountTl} TL</strong> indirim uygulandı. Ara Toplam: {$subtotalTl} TL &rarr; Ödenecek: <strong>{$totalTl} TL</strong></p>";
    }

    /** Sipariş e-postası (havale): transfer kodu, tutar, banka hesapları, dekont bilgisi */
    protected function buildOrderPaymentInfoHtml(Order $order): string
    {
        $totalTl = number_format($order->total_tl, 0, ',', '.');
        $code = e($order->payment_code);

        return "
                <p class=\"note\">Ödemenizi tamamlarken, açıklama kısmına özel transfer kodunuzu yazmayı unutmayınız. Bu kod, işleminizin doğru ve hızlı bir şekilde tanımlanabilmesi için gereklidir.</p>
                <div class=\"box\">Ödenecek Tutar: <strong>{$totalTl} TL</strong><br>Özel Transfer Kodunuz: <strong style=\"letter-spacing:1px;\">{$code}</strong></div>
                {$this->buildArtPuanUsedHtml($order)}
                <div class=\"box\">
                    <span class=\"label\">Banka Hesap Bilgileri</span><br><br>
                    <span class=\"muted\">Hesap Sahibi: BEARTSHARE ONLİNE SANAT GALERİSİ ANONİM ŞİRKETİ</span><br><br>
                    <strong>Garanti Bankası</strong> — Galatasaray Şubesi<br>
                    Hesap Numarası: 068 - 6291752<br>
                    IBAN: <strong>TR62 0006 2000 0680 0006 2917 52</strong><br><br>
                    <strong>Vakıflar Bankası</strong> — Levent Şubesi<br>
                    IBAN: <strong>TR68 0001 5001 5800 7321 4175 83</strong>
                </div>
                <p>Seçtiğiniz eserler sipariş oluşturulduktan itibaren 30 dakika boyunca sizin için rezerve edilir; bu süre içinde ödemenizi gerçekleştirmeniz gerekmektedir.</p>
                <p>Ödemenizi yaptıktan sonra dekontunuzu <a href=\"mailto:info@beartshare.com\" style=\"color:#14171c;\">info@beartshare.com</a> adresine gönderebilirsiniz. Ödemeniz onaylandıktan sonra sipariş süreciniz başlatılacaktır.</p>";
    }
}
