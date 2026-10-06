<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SortsIndex;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\InvoiceService;
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

    // ── Paraşüt'te yeni fatura ──

    /** Form: ?orders=190,191 seçilen siparişlerden otomatik doldurulur, her alan düzenlenebilir */
    public function create(Request $request, InvoiceService $service)
    {
        $ids = $this->orderIds((string) $request->query('orders'));
        $orders = Order::withTrashed()->with(['items.artwork', 'user', 'invoices'])->whereIn('id', $ids)->get();
        $missing = $ids->diff($orders->pluck('id'));

        $defaults = $orders->isNotEmpty() ? $service->defaults($orders) : null;
        $accounts = rescue(fn () => $service->accounts(), [], false);

        return view('admin.invoices.create', compact('orders', 'missing', 'defaults', 'accounts'));
    }

    public function store(Request $request, InvoiceService $service)
    {
        $data = $request->validate([
            'order_ids' => 'required|string',
            'contact_type' => 'required|in:person,company',
            'contact_name' => 'required|string|max:255',
            'tax_number' => ['required', 'regex:/^\d{10,11}$/'],
            'tax_office' => 'nullable|required_if:contact_type,company|string|max:100',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:30',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'issue_date' => 'required|date|before_or_equal:today|after_or_equal:' . now()->subDays(7)->toDateString(),
            'description' => 'nullable|string|max:255',
            'lines' => 'required|array|min:1',
            'lines.*.name' => 'required|string|max:255',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.vat_rate' => 'required|numeric|in:0,1,10,20',
            'record_payment' => 'nullable|boolean',
            'payment_account_id' => 'nullable|required_if:record_payment,1|string',
            'payment_date' => 'nullable|required_if:record_payment,1|date',
            'payment_amount' => 'nullable|required_if:record_payment,1|numeric|min:0.01',
        ], [
            'tax_number.regex' => 'TC 11, VKN 10 haneli olmalı.',
            'issue_date.after_or_equal' => 'e-Arşiv fatura tarihi en fazla 7 gün geriye verilebilir.',
            'tax_office.required_if' => 'Kurumsal fatura için vergi dairesi gerekli.',
        ]);
        $orders = Order::withTrashed()->whereIn('id', $this->orderIds($data['order_ids']))->get();
        abort_if($orders->isEmpty(), 422, 'Sipariş bulunamadı.');
        $data['lines'] = array_values($data['lines']);
        $data['record_payment'] = $request->boolean('record_payment');

        try {
            $invoice = $service->createDraft($data, $orders, auth()->id());
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Paraşüt\'te fatura oluşturulamadı: ' . $e->getMessage());
        }

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Paraşüt\'te taslak fatura oluşturuldu' . ($invoice->payment_recorded ? ' ve tahsilat işlendi' : '') . '. Kontrol edip resmileştirebilirsiniz.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('orders.items');

        return view('admin.invoices.show', compact('invoice'));
    }

    public function formalize(Invoice $invoice, InvoiceService $service)
    {
        try {
            $invoice = $service->formalize($invoice);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Resmileştirilemedi: ' . $e->getMessage());
        }

        return back()->with($invoice->status === 'failed' ? 'error' : 'success', match ($invoice->status) {
            'issued' => "{$invoice->e_document_label} olarak resmileşti: {$invoice->invoice_no}",
            'failed' => 'Paraşüt resmileştirme hatası: ' . $invoice->error,
            default => 'Paraşüt\'e gönderildi, işlem sürüyor. Biraz sonra "Durumu yenile" ile bakın.',
        });
    }

    public function refreshStatus(Invoice $invoice, InvoiceService $service)
    {
        try {
            $invoice = $service->refresh($invoice);
        } catch (\Throwable $e) {
            return back()->with('error', 'Paraşüt\'ten okunamadı: ' . $e->getMessage());
        }

        return back()->with('success', "Durum: {$invoice->status_label}" . ($invoice->invoice_no ? " · {$invoice->invoice_no}" : ''));
    }

    public function destroy(Invoice $invoice, InvoiceService $service)
    {
        try {
            $service->deleteDraft($invoice);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.invoices.index')->with('success', 'Taslak fatura Paraşüt\'ten silindi.');
    }

    /** "190, 1050191 #192" → [190, 191, 192] */
    protected function orderIds(string $value)
    {
        return collect(preg_split('/[\s,;]+/', $value, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($v) => (int) preg_replace('/^1050(?=\d)/', '', trim($v, '#')))
            ->filter()->unique()->values();
    }

    /** Tahmine dayalı eşleşmeyi doğru olarak işaretle */
    public function confirm(Invoice $invoice)
    {
        $invoice->update(['match_method' => 'manual', 'match_note' => trim(($invoice->match_note ? $invoice->match_note . '; ' : '') . 'admin onayladı')]);

        return back()->with('success', "{$invoice->invoice_no} eşleşmesi onaylandı.");
    }
}
