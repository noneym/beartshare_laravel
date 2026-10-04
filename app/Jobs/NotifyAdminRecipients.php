<?php

namespace App\Jobs;

use App\Models\NotificationRecipient;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Bir olayı (yeni sipariş vb.) Admin > Bildirim Alıcıları listesindeki kişilere
 * e-posta ve/veya SMS ile bildirir. Her gönderim Bildirim Log'a yazılır.
 */
class NotifyAdminRecipients implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Tekrar denenirse alıcılara mükerrer bildirim gider
    public int $tries = 1;
    public int $timeout = 120;

    public function __construct(
        public string $event,
        public string $subject,
        public string $htmlBody,
        public string $smsText,
        public ?int $orderId = null,
        public ?string $replyTo = null,
        public ?string $replyName = null,
        public ?string $skipEmail = null, // zaten gönderilmiş adres (ör. info@) — mükerrer olmasın
        public ?int $onlyRecipientId = null, // deneme gönderimi: yalnızca bu alıcı
    ) {
    }

    public function handle(NotificationService $notifications): void
    {
        $recipients = $this->onlyRecipientId
            ? NotificationRecipient::whereKey($this->onlyRecipientId)->get()
            : NotificationRecipient::for($this->event)->get();

        foreach ($recipients as $recipient) {
            if ($recipient->wantsEmail() && strcasecmp($recipient->email, (string) $this->skipEmail) !== 0) {
                $notifications->sendRecipientEmail($recipient->email, $this->subject, $this->htmlBody, "admin_{$this->event}", $this->orderId, $this->replyTo, $this->replyName);
            }
            if ($recipient->wantsSms()) {
                $notifications->sendSmsWithLog($recipient->phone, $this->smsText, "admin_{$this->event}", $this->orderId);
            }
        }
    }
}
