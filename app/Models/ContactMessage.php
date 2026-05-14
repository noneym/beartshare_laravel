<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'kvkk_accepted',
        'status',
        'ip_address',
        'admin_notes',
        'replied_at',
    ];

    protected $casts = [
        'kvkk_accepted' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public const STATUSES = [
        'new' => 'Yeni',
        'read' => 'Okundu',
        'replied' => 'Yanıtlandı',
        'closed' => 'Kapatıldı',
    ];

    public const SUBJECTS = [
        'genel' => 'Genel Bilgi',
        'satis' => 'Satış & Sipariş',
        'eser' => 'Eser Hakkında',
        'artpuan' => 'ArtPuan',
        'isbirligi' => 'İşbirliği Teklifi',
        'diger' => 'Diğer',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getSubjectLabelAttribute(): string
    {
        return self::SUBJECTS[$this->subject] ?? $this->subject;
    }
}
