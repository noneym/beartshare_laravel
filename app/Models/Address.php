<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'full_name',
        'phone',
        'city',
        'district',
        'address_line',
        'invoice_type',
        'tc_no',
        'company_name',
        'tax_office',
        'tax_number',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Adres tipini Türkçe döndür
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'shipping' => 'Teslimat Adresi',
            'billing' => 'Fatura Adresi',
            default => $this->type,
        };
    }

    /**
     * Fatura tipini Türkçe döndür
     */
    public function getInvoiceTypeLabelAttribute(): string
    {
        return match($this->invoice_type) {
            'individual' => 'Bireysel',
            'corporate' => 'Kurumsal',
            default => $this->invoice_type,
        };
    }

    /**
     * Tam adresi döndür
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->address_line}, {$this->district} / {$this->city}";
    }

    /**
     * Kısa özet
     */
    public function getSummaryAttribute(): string
    {
        $label = $this->title ?: $this->type_label;
        return "{$label} - {$this->district}/{$this->city}";
    }

    // ── Scopes ──

    public function scopeShipping($query)
    {
        return $query->where('type', 'shipping');
    }

    public function scopeBilling($query)
    {
        return $query->where('type', 'billing');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
