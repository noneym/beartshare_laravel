<?php

namespace App\Jobs;

use App\Models\Artwork;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Favori eser bildirimi (satıldı / fiyat değişti) kuyruktan gönderilir:
 * çok sayıda üyeye SMS + e-posta admin isteğini bekletmesin.
 */
class NotifyFavoriteWatchers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        public int $artworkId,
        public string $event,
        public int $excludeUserId = 0,
        public array $extra = [],
    ) {
    }

    public function handle(NotificationService $notifications): void
    {
        if ($artwork = Artwork::find($this->artworkId)) {
            $notifications->notifyFavoriteWatchers($artwork, $this->excludeUserId, $this->event, $this->extra);
        }
    }
}
