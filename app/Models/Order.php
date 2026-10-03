<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_method',
        'payment_code',
        'total_tl',
        'total_usd',
        'customer_name',
        'customer_email',
        'customer_phone',
        'tc_no',
        'shipping_address',
        'billing_address',
        'city',
        'district',
        'notes',
        'admin_notes',
        'shipping_company',
        'tracking_number',
        'confirmed_at',
        'paid_at',
        'artpuan_used',
        'discount_tl',
    ];

    protected $casts = [
        'total_tl' => 'decimal:2',
        'total_usd' => 'decimal:2',
        'artpuan_used' => 'decimal:2',
        'discount_tl' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Ödeme Bekleniyor',
            'paid' => 'Ödendi',
            'confirmed' => 'Onaylandı',
            'shipped' => 'Kargoda',
            'delivered' => 'Teslim Edildi',
            'cancelled' => 'İptal Edildi',
            'returned' => 'İade Edildi',
            'refunded' => 'Ücret İadesi Yapıldı',
            'payment_failed' => 'Ödeme Başarısız',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'confirmed' => 'blue',
            'shipped' => 'purple',
            'delivered' => 'green',
            'cancelled' => 'red',
            'returned' => 'gray',
            'refunded' => 'gray',
            'payment_failed' => 'red',
            default => 'gray',
        };
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'havale' => 'Havale / EFT',
            'kredi_karti' => 'Kredi Kartı',
            default => $this->payment_method,
        };
    }

    /**
     * Benzersiz havale ödeme kodu oluştur
     */
    public static function generatePaymentCode(): string
    {
        // Sadece rakam: banka açıklamasına kolay yazılır, karışan harf yok
        do {
            $code = (string) random_int(10000000, 99999999);
        } while (self::where('payment_code', $code)->exists());

        return $code;
    }
}
