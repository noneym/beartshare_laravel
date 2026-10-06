<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Admin'den Paraşüt faturası: siparişten varsayılan değerler → Paraşüt taslağı (+ tahsilat)
 * → resmileştirme (e-Fatura mükellefiyse e-Fatura, değilse e-Arşiv) → PDF.
 */
class InvoiceService
{
    public const DEFAULT_VAT = 20;
    // TC'si bilinmeyen bireysel alıcı için GİB'in kabul ettiği numara
    public const ANONYMOUS_TC = '11111111111';

    public function __construct(protected ParasutClient $parasut)
    {
    }

    // ── Varsayılan değerler ──

    /** Seçilen siparişlerden fatura formunun ön doldurması */
    public function defaults(Collection $orders): array
    {
        $first = $orders->first();
        $billing = $this->parseBilling((string) $first?->billing_address);
        $tax = $billing['tax_number'] ?? ($first?->tc_no ?: $first?->user?->tc_no);

        $lines = [];
        foreach ($orders as $order) {
            $itemsTotal = max(1, $order->items->sum(fn ($i) => (float) $i->price_tl * max(1, $i->quantity)));
            foreach ($order->items as $item) {
                $quantity = max(1, (int) $item->quantity);
                $commission = $this->commissionFromNote((string) $item->artwork?->sale_note);
                // Komisyon notu yoksa: siparişte ödenen tutarın bu kaleme düşen payı (KDV dahil → hariç)
                $share = (float) $order->total_tl * ((float) $item->price_tl * $quantity / $itemsTotal);
                $unitNet = $commission ?? round($share / $quantity / (1 + self::DEFAULT_VAT / 100), 2);
                $lines[] = [
                    'name' => $this->lineName($order, $item),
                    'quantity' => $quantity,
                    'unit_price' => $unitNet,
                    'vat_rate' => self::DEFAULT_VAT,
                    'source' => $commission !== null ? 'Satış notundaki komisyon' : 'Ödenen tutar',
                ];
            }
        }
        $total = collect($lines)->sum(fn ($l) => $l['unit_price'] * $l['quantity'] * (1 + $l['vat_rate'] / 100));

        return [
            'contact_type' => $billing ? 'company' : 'person',
            'contact_name' => $billing['company'] ?? (string) $first?->customer_name,
            'tax_number' => $tax ?: self::ANONYMOUS_TC,
            'tax_office' => $billing['tax_office'] ?? null,
            'email' => $first?->customer_email,
            'phone' => $first?->customer_phone,
            'address' => $billing['address'] ?? (string) $first?->shipping_address,
            'city' => $first?->city,
            'district' => $first?->district,
            'issue_date' => now()->toDateString(),
            'description' => 'ARACILIK',
            'lines' => $lines,
            'record_payment' => (bool) $orders->first(fn ($o) => $o->paid_at || in_array($o->status, ['paid', 'confirmed', 'shipped', 'delivered'], true)),
            'payment_account_id' => $this->defaultAccountId($first?->payment_method),
            'payment_date' => ($first?->paid_at ?? now())->toDateString(),
            'payment_amount' => round($total, 2),
        ];
    }

    /** "9.166.-TL+KDV" / "3.500 TL + KDV" → KDV hariç komisyon */
    public function commissionFromNote(string $note): ?float
    {
        $text = html_entity_decode(strip_tags($note), ENT_QUOTES, 'UTF-8');
        if (!preg_match('/(\d{1,3}(?:\.\d{3})+|\d+)(?:,(\d{1,2}))?\s*\.?\s*-?\s*TL\s*\+\s*KDV/iu', $text, $m)) {
            return null;
        }
        return (float) (str_replace('.', '', $m[1]) . (isset($m[2]) ? '.' . $m[2] : ''));
    }

    /** "1050190 MUSA GÜNEY 110 x 90 CM ARACILIK KOMİSYON ÜCRETİ" */
    public function lineName(Order $order, $item): string
    {
        $artist = trim(preg_replace('/\s*\(.*?\)\s*/u', ' ', (string) $item->artist_name));
        $dims = '';
        if (preg_match('/\d+(?:[.,]\d+)?\s*[xX×]\s*\d+(?:[.,]\d+)?(?:\s*[xX×]\s*\d+(?:[.,]\d+)?)?/u', (string) $item->artwork?->dimensions, $m)) {
            $dims = preg_replace('/\s*[xX×]\s*/u', ' x ', $m[0]) . ' CM';
        }
        $qty = (int) $item->quantity > 1 ? $item->quantity . ' ADET ' : '';

        return $this->upper(trim("1050{$order->id} {$qty}{$artist} {$dims}")) . ' ARACILIK KOMİSYON ÜCRETİ';
    }

    /** Checkout kurumsal fatura biçimi: "Firma | Vergi Dairesi V.D. VKN | adres" */
    protected function parseBilling(string $billing): ?array
    {
        if (!preg_match('/^(.+?)\s*\|\s*(.+?)\s+V\.D\.\s+(\d{10,11})\s*\|\s*(.+)$/u', $billing, $m)) {
            return null;
        }
        return ['company' => trim($m[1]), 'tax_office' => trim($m[2]), 'tax_number' => $m[3], 'address' => trim($m[4])];
    }

    protected function upper(string $s): string
    {
        return mb_strtoupper(strtr($s, ['i' => 'İ', 'ı' => 'I']), 'UTF-8');
    }

    // ── Kasa / banka hesapları ──

    /** @return array<string, string> id => ad */
    public function accounts(): array
    {
        return Cache::remember('parasut:accounts', now()->addHour(), function () {
            return collect($this->parasut->get('accounts', ['page[size]' => 50])['data'] ?? [])
                ->reject(fn ($a) => $a['attributes']['archived'] ?? false)
                ->mapWithKeys(fn ($a) => [$a['id'] => $a['attributes']['name']])
                ->all();
        });
    }

    protected function defaultAccountId(?string $paymentMethod): ?string
    {
        $accounts = rescue(fn () => $this->accounts(), [], false);
        // Havale / kart tahsilatı şirket banka hesabına, yoksa ilk hesap
        $bank = collect($accounts)->search(fn ($name) => str_contains(mb_strtolower($name), 'şirket'));
        return $bank ?: (array_key_first($accounts) ?: null);
    }

    // ── Taslak oluşturma ──

    /**
     * Paraşüt'te müşteri (yoksa) + satır ürünleri + taslak satış faturası (+ tahsilat).
     * @param array $data defaults() biçiminde, formdan düzenlenmiş
     */
    public function createDraft(array $data, Collection $orders, ?int $userId = null): Invoice
    {
        $contactId = $this->findOrCreateContact($data);

        $details = [];
        foreach ($data['lines'] as $line) {
            $product = $this->parasut->post('products', ['data' => ['type' => 'products', 'attributes' => [
                'name' => $line['name'],
                'vat_rate' => (float) $line['vat_rate'],
                'unit' => 'Adet',
                'list_price' => (float) $line['unit_price'],
                'currency' => 'TRL',
                'inventory_tracking' => false,
            ]]]);
            $details[] = [
                'type' => 'sales_invoice_details',
                'attributes' => [
                    'quantity' => (float) $line['quantity'],
                    'unit_price' => (float) $line['unit_price'],
                    'vat_rate' => (float) $line['vat_rate'],
                ],
                'relationships' => ['product' => ['data' => ['id' => $product['data']['id'], 'type' => 'products']]],
            ];
        }

        $created = $this->parasut->post('sales_invoices', ['data' => [
            'type' => 'sales_invoices',
            'attributes' => array_filter([
                'item_type' => 'invoice',
                'description' => $data['description'] ?: null,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['issue_date'],
                'currency' => 'TRL',
                'shipment_included' => false,
                'billing_address' => $data['address'] ?: null,
                'city' => $data['city'] ?: null,
                'district' => $data['district'] ?: null,
                'tax_number' => $data['tax_number'],
                'tax_office' => $data['tax_office'] ?: null,
                'billing_phone' => $data['phone'] ?: null,
            ], fn ($v) => $v !== null),
            'relationships' => [
                'details' => ['data' => $details],
                'contact' => ['data' => ['id' => $contactId, 'type' => 'contacts']],
            ],
        ]], ['include' => 'details']);
        $invoiceId = $created['data']['id'];
        $attr = $created['data']['attributes'];

        $paymentRecorded = false;
        if (!empty($data['record_payment']) && !empty($data['payment_account_id'])) {
            $this->parasut->post("sales_invoices/{$invoiceId}/payments", ['data' => ['type' => 'payments', 'attributes' => [
                'account_id' => $data['payment_account_id'],
                'date' => $data['payment_date'] ?: $data['issue_date'],
                'amount' => (float) $data['payment_amount'],
            ]]]);
            $paymentRecorded = true;
        }

        $invoice = Invoice::create([
            'status' => 'draft',
            'parasut_id' => $invoiceId,
            'parasut_contact_id' => $contactId,
            'issue_date' => $data['issue_date'],
            'description' => $data['description'] ?: null,
            'contact_name' => $data['contact_name'],
            'contact_tax_number' => $data['tax_number'],
            'contact_type' => $data['contact_type'],
            'party' => 'buyer',
            // Paraşüt: gross_total = KDV hariç, net_total = KDV dahil
            'net_total' => (float) ($attr['gross_total'] ?? 0),
            'vat_total' => (float) ($attr['total_vat'] ?? 0),
            'total' => (float) ($attr['net_total'] ?? 0),
            'payment_recorded' => $paymentRecorded,
            'lines' => collect($data['lines'])->map(fn ($l) => [
                'name' => $l['name'], 'quantity' => (float) $l['quantity'], 'unit_price' => (float) $l['unit_price'],
                'vat_rate' => (float) $l['vat_rate'],
                'total' => round($l['unit_price'] * $l['quantity'] * (1 + $l['vat_rate'] / 100), 2),
            ])->all(),
            'match_method' => 'manual',
            'match_note' => 'Admin panelden oluşturuldu',
            'created_by' => $userId,
            'synced_at' => now(),
        ]);
        $invoice->orders()->sync($orders->pluck('id')->all());

        return $invoice;
    }

    protected function findOrCreateContact(array $data): string
    {
        $tax = $data['tax_number'];
        if ($tax !== self::ANONYMOUS_TC) {
            $found = $this->parasut->get('contacts', ['filter[tax_number]' => $tax, 'page[size]' => 1])['data'][0]['id'] ?? null;
            if ($found) return $found;
        }

        return $this->parasut->post('contacts', ['data' => ['type' => 'contacts', 'attributes' => array_filter([
            'name' => $data['contact_name'],
            'contact_type' => $data['contact_type'] === 'company' ? 'company' : 'person',
            'tax_number' => $tax,
            'tax_office' => $data['tax_office'] ?: null,
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null,
            'address' => $data['address'] ?: null,
            'city' => $data['city'] ?: null,
            'district' => $data['district'] ?: null,
            'account_type' => 'customer',
        ], fn ($v) => $v !== null)]])['data']['id'];
    }

    // ── Resmileştirme ──

    /** e-Fatura mükellefi VKN → e-Fatura, değilse e-Arşiv. İş Paraşüt'te arka planda yürür. */
    public function formalize(Invoice $invoice): Invoice
    {
        if (!$invoice->isDraft()) {
            throw new RuntimeException('Yalnızca taslak fatura resmileştirilebilir.');
        }

        $inbox = $invoice->contact_tax_number && $invoice->contact_tax_number !== self::ANONYMOUS_TC
            ? ($this->parasut->get('e_invoice_inboxes', ['filter[vkn]' => $invoice->contact_tax_number])['data'][0]['attributes']['e_invoice_address'] ?? null)
            : null;

        $job = $inbox
            ? $this->parasut->post('e_invoices', ['data' => [
                'type' => 'e_invoices',
                'attributes' => ['scenario' => 'basic', 'to' => $inbox],
                'relationships' => ['invoice' => ['data' => ['id' => $invoice->parasut_id, 'type' => 'sales_invoices']]],
            ]])
            : $this->parasut->post('e_archives', ['data' => [
                'type' => 'e_archives',
                'relationships' => ['sales_invoice' => ['data' => ['id' => $invoice->parasut_id, 'type' => 'sales_invoices']]],
            ]]);

        $invoice->update([
            'status' => 'formalizing',
            'e_document_type' => $inbox ? 'e_invoice' : 'e_archive',
            'trackable_job_id' => $job['data']['id'] ?? null,
            'error' => null,
        ]);

        // Çoğunlukla birkaç saniyede biter; bitmezse "Durumu yenile" ile sonra bakılır
        for ($i = 0; $i < 6 && $invoice->status === 'formalizing'; $i++) {
            sleep(2);
            $this->refresh($invoice);
        }

        return $invoice->fresh();
    }

    /** Resmileştirme işinin durumu; bittiyse e-belge bilgisi ve PDF */
    public function refresh(Invoice $invoice): Invoice
    {
        if ($invoice->status === 'formalizing' && $invoice->trackable_job_id) {
            $job = $this->parasut->get("trackable_jobs/{$invoice->trackable_job_id}")['data']['attributes'] ?? [];
            if (($job['status'] ?? null) === 'error') {
                $invoice->update(['status' => 'failed', 'error' => implode('; ', (array) ($job['errors'] ?? ['Paraşüt resmileştirme hatası']))]);
                return $invoice;
            }
            if (($job['status'] ?? null) !== 'done') {
                return $invoice;
            }
        }

        $si = $this->parasut->get("sales_invoices/{$invoice->parasut_id}", ['include' => 'active_e_document']);
        $ed = $si['data']['relationships']['active_e_document']['data'] ?? null;
        $edAttr = collect($si['included'] ?? [])->firstWhere('id', $ed['id'] ?? null)['attributes'] ?? [];
        $attr = $si['data']['attributes'];

        $invoice->update(array_filter([
            'status' => $ed ? 'issued' : $invoice->status,
            'invoice_no' => $attr['invoice_no'] ?? null,
            'e_document_type' => $ed ? ($ed['type'] === 'e_archives' ? 'e_archive' : 'e_invoice') : null,
            'e_document_id' => $ed['id'] ?? null,
            'e_document_status' => $edAttr['status'] ?? null,
            'e_document_uuid' => $edAttr['uuid'] ?? null,
            'net_total' => isset($attr['gross_total']) ? (float) $attr['gross_total'] : null,
            'vat_total' => isset($attr['total_vat']) ? (float) $attr['total_vat'] : null,
            'total' => isset($attr['net_total']) ? (float) $attr['net_total'] : null,
            'synced_at' => now(),
        ], fn ($v) => $v !== null));

        if ($invoice->e_document_id && !$invoice->pdf_path) {
            $this->downloadPdf($invoice);
        }

        return $invoice;
    }

    public function downloadPdf(Invoice $invoice): bool
    {
        $type = $invoice->e_document_type === 'e_invoice' ? 'e_invoices' : 'e_archives';
        $pdf = $this->parasut->eDocumentPdf($type, $invoice->e_document_id);
        if (!$pdf) return false;

        $path = 'invoices/' . $invoice->issue_date->format('Y') . '/' . ($invoice->invoice_no ?: 'parasut-' . $invoice->parasut_id) . '.pdf';
        Storage::disk(config('filesystems.uploads'))->put($path, $pdf, ['visibility' => 'private', 'ContentType' => 'application/pdf']);
        $invoice->update(['pdf_path' => $path]);

        return true;
    }

    /** Resmileşmemiş taslağı Paraşüt'ten ve buradan siler */
    public function deleteDraft(Invoice $invoice): void
    {
        if (!$invoice->isDraft()) {
            throw new RuntimeException('Resmileşmiş fatura silinemez; Paraşüt\'ten iptal / iade faturası kesilmelidir.');
        }
        $this->parasut->delete("sales_invoices/{$invoice->parasut_id}");
        $invoice->delete();
    }
}
