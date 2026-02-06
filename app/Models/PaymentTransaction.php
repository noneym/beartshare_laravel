<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'gateway',
        'amount',
        'currency',
        'status',
        'installment_count',
        'gateway_transaction_id',
        'auth_code',
        'host_ref_num',
        'card_number',
        'error_code',
        'error_message',
        'request_data',
        'response_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'installment_count' => 'integer',
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    /**
     * Sipariş ilişkisi
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Başarılı mı?
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Beklemede mi?
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Başarısız mı?
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Status badge rengi
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            'refunded' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Status Türkçe
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Başarılı',
            'pending' => 'Beklemede',
            'failed' => 'Başarısız',
            'refunded' => 'İade Edildi',
            default => 'Bilinmiyor',
        };
    }

    /**
     * Formatlanmış tutar
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', '.') . ' TL';
    }
}
