<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArtPuanLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_id',
        'artwork_id',
        'source_user_id',
        'type',
        'amount',
        'balance_after',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // ── İlişkiler ──

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }

    /**
     * Referans kaynağı kullanıcı
     */
    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    // ── Scope'lar ──

    public function scopePurchase($query)
    {
        return $query->where('type', 'purchase');
    }

    public function scopeReferral($query)
    {
        return $query->where('type', 'referral');
    }

    public function scopeBonus($query)
    {
        return $query->where('type', 'bonus');
    }

    public function scopeSpend($query)
    {
        return $query->where('type', 'spend');
    }

    // ── Accessors ──

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'purchase' => 'Satın Alma',
            'referral' => 'Referans',
            'bonus' => 'Bonus',
            'manual' => 'Manuel',
            'refund' => 'İade',
            'spend' => 'Harcama',
            default => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'purchase' => 'text-green-600 bg-green-50',
            'referral' => 'text-blue-600 bg-blue-50',
            'bonus' => 'text-purple-600 bg-purple-50',
            'manual' => 'text-gray-600 bg-gray-50',
            'refund' => 'text-red-600 bg-red-50',
            'spend' => 'text-orange-600 bg-orange-50',
            default => 'text-gray-600 bg-gray-50',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->amount >= 0 ? '+' : '';
        return $prefix . number_format($this->amount, 2, ',', '.') . ' AP';
    }
}
