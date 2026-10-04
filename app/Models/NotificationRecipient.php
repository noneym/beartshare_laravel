<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Yönetici bildirim alıcısı: hangi olaylarda, hangi kanaldan (e-posta / SMS) haber alacağı.
 */
class NotificationRecipient extends Model
{
    public const EVENTS = [
        'new_order' => 'Yeni sipariş',
        'contact_message' => 'İletişim formu mesajı',
        'artwork_submission' => 'Eser başvurusu',
    ];

    protected $fillable = ['name', 'email', 'phone', 'via_email', 'via_sms', 'events', 'is_active'];

    protected $casts = [
        'via_email' => 'boolean',
        'via_sms' => 'boolean',
        'is_active' => 'boolean',
        'events' => 'array',
    ];

    public function scopeFor($query, string $event)
    {
        return $query->where('is_active', true)->whereJsonContains('events', $event);
    }

    public function wantsEmail(): bool
    {
        return $this->via_email && $this->email;
    }

    public function wantsSms(): bool
    {
        return $this->via_sms && $this->phone;
    }
}
