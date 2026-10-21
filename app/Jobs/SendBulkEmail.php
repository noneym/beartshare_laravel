<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use App\Support\MessageTemplate;
use App\Support\NotificationTemplates;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Toplu e-posta: alıcı başına bir iş. Sonuç Bildirim Log'a yazılır (NotificationService).
 */
class SendBulkEmail implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 60;

    /** $layout: gövde, bildirim e-postalarındaki ortak çerçeveyle (logo, kart, altbilgi) sarılır */
    public function __construct(public int $userId, public string $subject, public string $body, public bool $layout = false)
    {
    }

    public function handle(NotificationService $notifications): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $user = User::find($this->userId);
        if (!$user?->email) {
            return;
        }

        $body = MessageTemplate::render($this->body, $user);

        $sent = $notifications->sendAdminEmail(
            $user->email,
            MessageTemplate::render($this->subject, $user),
            $this->layout ? NotificationTemplates::layout($body) : $body,
            $user->id
        );

        if (!$sent) {
            $this->fail(new \RuntimeException("E-posta gönderilemedi: {$user->email} (ayrıntı Bildirim Log'da)"));
        }
    }
}
