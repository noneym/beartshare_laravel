<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'type',
        'recipient',
        'subject',
        'message',
        'status',
        'error',
        'api_response',
        'order_id',
        'user_id',
    ];

    // ── Relations ──

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ──

    public function getChannelLabelAttribute(): string
    {
        return match ($this->channel) {
            'sms' => 'SMS',
            'email' => 'E-posta',
            default => $this->channel,
        };
    }

    public function getChannelColorAttribute(): string
    {
        return match ($this->channel) {
            'sms' => 'bg-blue-100 text-blue-800',
            'email' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'success' => 'Basarili',
            'failed' => 'Basarisiz',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'success' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'order_created' => 'Siparis Olusturuldu',
            'buyer_artpuan' => 'Alici ArtPuan',
            'referrer_artpuan' => 'Referans ArtPuan',
            'favorite_reserved' => 'Favori Rezerve',
            'sms_verification' => 'SMS Dogrulama',
            'sms_verification_resend' => 'SMS Dogrulama (Tekrar)',
            'admin_sms' => 'Admin SMS',
            'admin_email' => 'Admin E-posta',
            default => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'order_created' => 'bg-amber-100 text-amber-800',
            'buyer_artpuan' => 'bg-green-100 text-green-800',
            'referrer_artpuan' => 'bg-blue-100 text-blue-800',
            'favorite_reserved' => 'bg-pink-100 text-pink-800',
            'sms_verification', 'sms_verification_resend' => 'bg-indigo-100 text-indigo-800',
            'admin_sms' => 'bg-cyan-100 text-cyan-800',
            'admin_email' => 'bg-fuchsia-100 text-fuchsia-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
