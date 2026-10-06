<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SortsIndex;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Admin > Faturalar: Paraşüt'ten aktarılan satış faturaları, siparişlerle eşleşme, PDF.
 * İçe aktarma / güncelleme: php artisan parasut:import-invoices --pdf
 */
class InvoiceController extends Controller
{
    use SortsIndex;

    public function index(Request $request)
    {
        $query = Invoice::with(['orders:id,order_number,customer_name']);

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('invoice_no', 'like', "%{$s}%")
                  ->orWhere('contact_name', 'like', "%{$s}%")
                  ->orWhere('contact_tax_number', 'like', "%{$s}%")
                  ->orWhere('lines', 'like', "%{$s}%")
                  ->orWhereHas('orders', fn ($o) => $o->where('orders.id', $s)->orWhere('order_number', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('match')) {
            match ($request->input('match')) {
                'none' => $query->whereNull('match_method'),
                'review' => $query->whereIn('match_method', ['tax_number', 'artwork']),
                default => $query->where('match_method', $request->input('match')),
            };
        }
        if ($request->filled('party')) {
            $query->where('party', $request->input('party'));
        }
        if ($request->filled('year')) {
            $query->whereYear('issue_date', $request->input('year'));
        }

        $this->applySort($query, $request, [
            'no' => 'invoice_no',
            'date' => 'issue_date',
            'contact' => 'contact_name',
            'party' => 'party',
            'total' => 'total',
            'match' => 'match_method',
        ]) || $query->orderByDesc('issue_date')->orderByDesc('id');

        $invoices = $query->paginate(30)->withQueryString();

        $stats = [
            'count' => Invoice::count(),
            'total' => Invoice::sum('total'),
            'unmatched' => Invoice::whereNull('match_method')->count(),
            'review' => Invoice::whereIn('match_method', ['tax_number', 'artwork'])->count(),
            'no_pdf' => Invoice::whereNull('pdf_path')->whereNotNull('e_document_id')->count(),
            'last_sync' => Invoice::max('synced_at'),
        ];
        $years = Invoice::selectRaw('YEAR(issue_date) y')->distinct()->orderByDesc('y')->pluck('y');

        return view('admin.invoices.index', compact('invoices', 'stats', 'years'));
    }

    /** PDF admin oturumuyla verilir; R2'de herkese açık adres yok */
    public function pdf(Invoice $invoice)
    {
        $disk = Storage::disk(config('filesystems.uploads'));
        abort_unless($invoice->pdf_path && $disk->exists($invoice->pdf_path), 404);

        return response($disk->get($invoice->pdf_path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . ($invoice->invoice_no ?: 'fatura-' . $invoice->id) . '.pdf"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /** Elle sipariş bağlama: "190, 191" gibi sipariş id'leri (boş = bağlantıyı kaldır) */
    public function link(Request $request, Invoice $invoice)
    {
        $ids = collect(preg_split('/[\s,;]+/', (string) $request->input('order_ids'), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($v) => (int) preg_replace('/^1050(?=\d)/', '', trim($v, '#'))) // "1050190" de kabul
            ->filter()->unique();
        $found = Order::withTrashed()->whereIn('id', $ids)->pluck('id');
        $missing = $ids->diff($found);
        if ($missing->isNotEmpty()) {
            return back()->with('error', 'Sipariş bulunamadı: #' . $missing->implode(', #'));
        }

        $invoice->orders()->sync($found->all());
        $invoice->update([
            'match_method' => $found->isNotEmpty() ? 'manual' : null,
            'match_note' => $found->isNotEmpty() ? 'Admin tarafından bağlandı' : null,
        ]);

        return back()->with('success', "{$invoice->invoice_no}: " . ($found->isNotEmpty() ? 'sipariş #' . $found->implode(', #') . ' bağlandı.' : 'bağlantı kaldırıldı.'));
    }

    /** Tahmine dayalı eşleşmeyi doğru olarak işaretle */
    public function confirm(Invoice $invoice)
    {
        $invoice->update(['match_method' => 'manual', 'match_note' => trim(($invoice->match_note ? $invoice->match_note . '; ' : '') . 'admin onayladı')]);

        return back()->with('success', "{$invoice->invoice_no} eşleşmesi onaylandı.");
    }
}
