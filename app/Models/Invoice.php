<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public const MATCH_METHODS = [
        'code' => 'Sipariş kodu (1050…)',
        'order_ref' => 'Sipariş no (SİP. …)',
        'tax_number' => 'Alıcı TC/VKN + tarih',
        'artwork' => 'Sanatçı + ölçü + tarih',
        'manual' => 'Elle',
    ];

    protected $fillable = [
        'parasut_id', 'invoice_no', 'issue_date', 'description',
        'contact_name', 'contact_tax_number', 'contact_type', 'party',
        'net_total', 'vat_total', 'total', 'currency', 'lines',
        'e_document_type', 'e_document_id', 'e_document_status', 'e_document_uuid', 'pdf_path',
        'match_method', 'match_note', 'synced_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'net_total' => 'decimal:2',
        'vat_total' => 'decimal:2',
        'total' => 'decimal:2',
        'lines' => 'array',
        'synced_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withTrashed();
    }

    public function getPartyLabelAttribute(): ?string
    {
        return match ($this->party) {
            'buyer' => 'Alıcı',
            'seller' => 'Satıcı',
            default => null,
        };
    }

    public function getEDocumentLabelAttribute(): ?string
    {
        return match ($this->e_document_type) {
            'e_archive' => 'e-Arşiv',
            'e_invoice' => 'e-Fatura',
            default => null,
        };
    }

    /** Tahmine dayalı eşleşme (TC / eser) — admin'in gözden geçirmesi önerilir */
    public function isFuzzyMatch(): bool
    {
        return in_array($this->match_method, ['tax_number', 'artwork'], true);
    }
}
