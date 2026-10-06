<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Hesabım > Siparişlerim: müşterinin kendi alıcı faturasının PDF'i.
 * Yalnızca siparişin sahibi; yalnızca alıcıya kesilmiş ve resmileşmiş faturalar.
 */
class AccountInvoiceController extends Controller
{
    public function show(Invoice $invoice)
    {
        $allowed = $invoice->party === 'buyer'
            && $invoice->status === 'issued'
            && $invoice->pdf_path
            && $invoice->orders()->where('orders.user_id', Auth::id())->exists();
        abort_unless($allowed, 404);

        $disk = Storage::disk(config('filesystems.uploads'));
        abort_unless($disk->exists($invoice->pdf_path), 404);

        return response($disk->get($invoice->pdf_path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="BeArtShare-Fatura-' . ($invoice->invoice_no ?: $invoice->id) . '.pdf"',
            'Cache-Control' => 'private, max-age=3600',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
