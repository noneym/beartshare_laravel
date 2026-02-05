<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'artwork_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }

    public static function getCartItems($userId = null, $sessionId = null)
    {
        return static::with('artwork.artist')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId && $sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->get();
    }
}
