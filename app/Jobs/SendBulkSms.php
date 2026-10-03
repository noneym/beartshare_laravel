<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use App\Support\MessageTemplate;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Toplu SMS: alıcı başına bir iş. Sonuç Bildirim Log'a yazılır (NotificationService).
 */
class SendBulkSms implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Gönderim tekrarlanırsa kullanıcıya iki SMS gider; tekrar deneme yok
    public int $tries = 1;
    public int $timeout = 60;

    public function __construct(public int $userId, public string $message)
    {
    }

    public function handle(NotificationService $notifications): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $user = User::find($this->userId);
        if (!$user?->phone) {
            return;
        }

        $result = $notifications->sendSmsWithLog(
            $user->phone,
            MessageTemplate::render($this->message, $user),
            'admin_sms',
            null,
            $user->id
        );

        if (!($result['success'] ?? false)) {
            $this->fail(new \RuntimeException('SMS gönderilemedi: ' . ($result['message'] ?? $result['error'] ?? 'bilinmeyen hata')));
        }
    }
}
