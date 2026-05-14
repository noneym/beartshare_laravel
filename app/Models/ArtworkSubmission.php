<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtworkSubmission extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'artist_name',
        'artwork_title',
        'technique',
        'dimensions',
        'year',
        'expected_price',
        'notes',
        'images',
        'status',
        'admin_notes',
        'ip_address',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public const STATUSES = [
        'new' => 'Yeni',
        'reviewing' => 'İnceleniyor',
        'accepted' => 'Kabul Edildi',
        'rejected' => 'Reddedildi',
        'closed' => 'Kapatıldı',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
