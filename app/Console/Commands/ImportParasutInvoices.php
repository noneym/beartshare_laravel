<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use App\Services\ParasutClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Paraşüt satış faturalarını içe aktarır, siparişlerle eşleştirir, PDF'leri yükleme diskine alır.
 * Tekrar çalıştırılabilir: Paraşüt id'sine göre günceller; elle yapılan eşleştirmelere dokunmaz.
 *
 * Eşleştirme sırası (ilk tutan kullanılır):
 *  1. code       ürün adında "1050{sipariş id}" (eski sistemde havale açıklama kodu)
 *  2. order_ref  ürün adında "SİP. {sipariş id}"
 *  3. tax_number faturadaki TC/VKN = siparişteki alıcının, ödeme tarihine ±20 gün
 *  4. artwork    ürün adında sanatçı soyadı (+ varsa ölçü), ödeme tarihine ±30 gün (satıcı faturaları)
 */
class ImportParasutInvoices extends Command
{
    protected $signature = 'parasut:import-invoices
        {--pdf : e-Arşiv / e-Fatura PDF\'lerini de indir (yalnızca eksik olanlar)}
        {--dry-run : Kaydetmeden yalnızca eşleştirme sonucunu göster}';

    protected $description = 'Paraşüt satış faturalarını içe aktarır ve siparişlerle eşleştirir';

    protected Collection $orders;
    protected Collection $userTc;

    public function handle(): int
    {
        $client = new ParasutClient();
        $this->info('Paraşüt faturaları çekiliyor...');
        $dump = $client->salesInvoices();
        $inc = $dump['included'];
        $this->info(count($dump['data']) . ' fatura alındı.');

        $this->orders = Order::withTrashed()
            ->with(['items' => fn ($q) => $q->withTrashed(), 'items.artwork:id,dimensions'])
            ->whereNotIn('status', ['pending', 'payment_failed'])
            ->get();
        $this->userTc = User::whereNotNull('tc_no')->pluck('tc_no', 'id');

        $stats = array_fill_keys(array_keys(Invoice::MATCH_METHODS), 0) + ['none' => 0, 'pdf' => 0, 'pdf_fail' => 0];
        $unmatched = [];

        foreach ($dump['data'] as $raw) {
            $a = $raw['attributes'];
            $contact = ($cid = $raw['relationships']['contact']['data']['id'] ?? null) ? ($inc["contacts:{$cid}"]['attributes'] ?? []) : [];
            $lines = $this->lines($raw, $inc);
            $text = implode(' | ', array_column($lines, 'name'));
            $date = Carbon::parse($a['issue_date']);
            $tax = $contact['tax_number'] ?? null;

            $existing = Invoice::where('parasut_id', $raw['id'])->first();
            if ($existing && $existing->match_method === 'manual') {
                [$method, $orders, $note] = ['manual', $existing->orders, $existing->match_note];
            } else {
                [$method, $orders, $note] = $this->match($text, $tax, $date);
            }
            $method ? $stats[$method]++ : $stats['none']++;
            if (!$method) {
                $unmatched[] = "{$a['invoice_no']} {$a['issue_date']} " . ($contact['name'] ?? '-') . " — " . mb_substr($text, 0, 60);
            }

            $ed = $raw['relationships']['active_e_document']['data'] ?? null;
            $edAttr = $ed ? ($inc["{$ed['type']}:{$ed['id']}"]['attributes'] ?? []) : [];

            $values = [
                'invoice_no' => $a['invoice_no'] ?: null,
                'issue_date' => $a['issue_date'],
                'description' => $a['description'] ?: null,
                'contact_name' => $contact['name'] ?? '-',
                'contact_tax_number' => $tax,
                'contact_type' => $contact['contact_type'] ?? null,
                'party' => $this->party($text, $tax, $orders),
                // Paraşüt: gross_total = KDV hariç, net_total = KDV dahil
                'net_total' => (float) $a['gross_total'],
                'vat_total' => (float) $a['total_vat'],
                'total' => (float) $a['net_total'],
                'currency' => ($a['currency'] ?? 'TRL') === 'TRL' ? 'TRY' : $a['currency'],
                'lines' => $lines,
                'e_document_type' => $ed ? ($ed['type'] === 'e_archives' ? 'e_archive' : 'e_invoice') : null,
                'e_document_id' => $ed['id'] ?? null,
                'e_document_status' => $edAttr['status'] ?? null,
                'e_document_uuid' => $edAttr['uuid'] ?? null,
                'match_method' => $method,
                'match_note' => $note,
                'synced_at' => now(),
            ];

            if ($this->option('dry-run')) continue;

            $invoice = Invoice::updateOrCreate(['parasut_id' => $raw['id']], $values);
            $invoice->orders()->sync($orders->pluck('id')->all());

            if ($this->option('pdf') && $ed && !$invoice->pdf_path) {
                $pdf = rescue(fn () => $client->eDocumentPdf($ed['type'], $ed['id']), null, false);
                if ($pdf) {
                    $path = 'invoices/' . $date->format('Y') . '/' . ($invoice->invoice_no ?: 'parasut-' . $raw['id']) . '.pdf';
                    Storage::disk(config('filesystems.uploads'))->put($path, $pdf, ['visibility' => 'private', 'ContentType' => 'application/pdf']);
                    $invoice->update(['pdf_path' => $path]);
                    $stats['pdf']++;
                } else {
                    $stats['pdf_fail']++;
                    $this->line("  <fg=yellow>PDF alınamadı</> {$invoice->invoice_no}");
                }
            }
        }

        $this->newLine();
        $this->table(['Eşleşme', 'Fatura'], collect(Invoice::MATCH_METHODS)->map(fn ($l, $k) => [$l, $stats[$k]])->push(['Eşleşmedi', $stats['none']])->values()->all());
        if ($this->option('pdf')) $this->info("PDF: {$stats['pdf']} yüklendi, {$stats['pdf_fail']} alınamadı");
        foreach ($unmatched as $u) $this->line("  <fg=yellow>eşleşmedi</> {$u}");
        if ($this->option('dry-run')) $this->warn('[DRY RUN] Kayıt yapılmadı.');

        return self::SUCCESS;
    }

    /** @return array<int, array{name: string, quantity: float, unit_price: float, vat_rate: float, total: float}> */
    protected function lines(array $raw, array $inc): array
    {
        $lines = [];
        foreach ($raw['relationships']['details']['data'] ?? [] as $ref) {
            $d = $inc["sales_invoice_details:{$ref['id']}"] ?? null;
            if (!$d) continue;
            $pid = $d['relationships']['product']['data']['id'] ?? null;
            $lines[] = [
                'name' => trim(($d['attributes']['description'] ?: '') ?: ($pid ? ($inc["products:{$pid}"]['attributes']['name'] ?? '') : '')),
                'quantity' => (float) $d['attributes']['quantity'],
                'unit_price' => round((float) $d['attributes']['unit_price'], 2),
                'vat_rate' => (float) $d['attributes']['vat_rate'],
                'total' => (float) $d['attributes']['net_total'],
            ];
        }
        return $lines;
    }

    /** @return array{0: ?string, 1: Collection, 2: ?string} [yöntem, siparişler, not] */
    protected function match(string $text, ?string $tax, Carbon $date): array
    {
        // 1-2) Ürün adındaki sipariş kodu (bir faturada birden fazla olabilir)
        preg_match_all('/\b1050(\d{1,4})\b/', $text, $m);
        $byCode = $this->orders->whereIn('id', array_map('intval', $m[1]));
        if ($byCode->isNotEmpty()) return ['code', $byCode->values(), null];

        preg_match_all('/S[İI]P\.?\s*(?:NO\s*:?\s*)?(\d{1,4})\b/u', $text, $m);
        $byRef = $this->orders->whereIn('id', array_map('intval', $m[1]));
        if ($byRef->isNotEmpty()) return ['order_ref', $byRef->values(), null];

        // Satıcı (konsinye sahibi) faturasında TC alıcıya ait değildir: doğrudan eser eşleştirmesi
        $seller = str_contains($this->norm($text), 'SATICI');

        // 3) Alıcı TC/VKN + tarih
        if (!$seller && $tax && !preg_match('/^(\d)\1+$/', $tax)) {
            $byTax = $this->near($this->orders->filter(fn ($o) => $o->tc_no === $tax || ($this->userTc[$o->user_id] ?? null) === $tax), $date, 20);
            if ($byTax) return ['tax_number', collect([$byTax]), 'TC/VKN ve tarih ile eşleşti'];
        }

        // 4) Satır satır: sanatçı soyadı (+ ölçü) + tarih — satıcı faturası birden fazla siparişi kapsayabilir
        $found = collect();
        $loose = false;
        foreach (explode(' | ', $text) as $line) {
            $norm = $this->norm($line);
            preg_match_all('/(\d{2,3}(?:[,.]\d)?)\s*[Xx×]\s*(\d{2,3}(?:[,.]\d)?)/u', $line, $dims, PREG_SET_ORDER);
            $order = $this->near($this->orders->filter(fn ($o) => $this->artworkMatches($o, $norm, $dims)), $date, 30);

            // Ölçü yanlış yazılmış olabilir (ör. 700x100): sanatçı tutuyor ve aralıkta tek aday varsa
            if (!$order && $dims) {
                $candidates = $this->orders->filter(fn ($o) => $o->status !== 'cancelled' && $this->artworkMatches($o, $norm, []))
                    ->filter(fn ($o) => abs(Carbon::parse($o->paid_at ?? $o->created_at)->startOfDay()->diffInDays($date->copy()->startOfDay())) <= 30);
                if ($candidates->count() === 1) {
                    $order = $candidates->first();
                    $loose = true;
                }
            }
            if ($order) $found->put($order->id, $order);
        }
        if ($found->isNotEmpty()) {
            $note = 'Sanatçı / ölçü ve tarih ile eşleşti' . ($found->count() > 1 ? " ({$found->count()} sipariş)" : '') . ($loose ? '; ölçü uyuşmadı, tek aday' : '');
            return ['artwork', $found->values(), $note];
        }

        return [null, collect(), null];
    }

    /** Siparişteki bir eser, fatura satırındaki sanatçı soyadı (+ verildiyse ölçü) ile tutuyor mu */
    protected function artworkMatches(Order $order, string $normLine, array $dims): bool
    {
        foreach ($order->items as $item) {
            $parts = explode(' ', $this->norm(preg_replace('/\(.*?\)/', '', (string) $item->artist_name)));
            $surname = end($parts);
            if (mb_strlen((string) $surname) < 3 || !preg_match('/\b' . preg_quote($surname, '/') . '\b/u', $normLine)) continue;
            if (!$dims) return true;
            $d = preg_replace('/\s+/', '', strtolower((string) $item->artwork?->dimensions));
            foreach ($dims as $dm) {
                if (str_contains($d, strtolower("{$dm[1]}x{$dm[2]}")) || str_contains($d, strtolower("{$dm[2]}x{$dm[1]}"))) return true;
            }
        }
        return false;
    }

    /** Fatura tarihine en yakın (± $days gün) sipariş; iptal edilmişler yalnızca başka aday yoksa */
    protected function near(Collection $orders, Carbon $date, int $days): ?Order
    {
        return $orders
            ->map(fn ($o) => [$o, abs(Carbon::parse($o->paid_at ?? $o->confirmed_at ?? $o->created_at)->startOfDay()->diffInDays($date->copy()->startOfDay()))])
            ->filter(fn ($x) => $x[1] <= $days)
            ->sortBy(fn ($x) => [$x[0]->status === 'cancelled' ? 1 : 0, $x[1]])
            ->first()[0] ?? null;
    }

    /** Alıcı mı satıcı mı: metinde ALICI/SATICI, yoksa TC siparişin alıcısıyla aynıysa alıcı */
    protected function party(string $text, ?string $tax, Collection $orders): ?string
    {
        $n = $this->norm($text);
        if (str_contains($n, 'SATICI')) return 'seller';
        if (str_contains($n, 'ALICI') || str_contains($n, 'AKICI')) return 'buyer';
        if ($orders->isEmpty() || !$tax) return null;

        return $orders->contains(fn ($o) => $o->tc_no === $tax || ($this->userTc[$o->user_id] ?? null) === $tax) ? 'buyer' : 'seller';
    }

    protected function norm(?string $s): string
    {
        return preg_replace('/\s+/', ' ', strtr(mb_strtoupper((string) $s, 'UTF-8'), ['İ' => 'I', 'Ş' => 'S', 'Ğ' => 'G', 'Ü' => 'U', 'Ö' => 'O', 'Ç' => 'C', 'Â' => 'A', 'Î' => 'I']));
    }
}
