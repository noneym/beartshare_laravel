<x-admin.layouts.app>
    <x-slot name="title">Fatura {{ $invoice->invoice_no ?: '#' . $invoice->id }}</x-slot>

    @php
        $statusColor = match ($invoice->status) {
            'issued' => 'bg-green-100 text-green-800',
            'formalizing' => 'bg-blue-100 text-blue-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-amber-100 text-amber-800',
        };
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <a href="{{ route('admin.invoices.index') }}" class="text-sm text-gray-500 hover:underline">← Faturalar</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-1 flex items-center gap-3">
                {{ $invoice->invoice_no ?: 'Taslak fatura' }}
                <span class="text-xs px-2 py-1 rounded-full {{ $statusColor }}">{{ $invoice->status_label }}</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $invoice->issue_date->format('d.m.Y') }}
                @if($invoice->e_document_label) · {{ $invoice->e_document_label }} @endif
                @if($invoice->e_document_status) · Paraşüt: {{ $invoice->e_document_status }} @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($invoice->isDraft())
                <form method="POST" action="{{ route('admin.invoices.formalize', $invoice) }}"
                      onsubmit="return confirm('Fatura resmileştirilsin mi? VKN e-Fatura mükellefiyse e-Fatura, değilse e-Arşiv olarak GİB\'e gönderilir. Bu işlem geri alınamaz; sonrasında yalnızca iptal / iade faturasıyla düzeltilebilir.')">
                    @csrf
                    <button class="px-4 py-2 bg-primary text-white rounded text-sm font-medium hover:opacity-90">Resmileştir (e-Arşiv / e-Fatura)</button>
                </form>
                <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" onsubmit="return confirm('Taslak fatura Paraşüt\'ten silinsin mi?')">
                    @csrf @method('DELETE')
                    <button class="px-4 py-2 border border-red-200 text-red-600 rounded text-sm hover:bg-red-50">Taslağı sil</button>
                </form>
            @endif
            @if(in_array($invoice->status, ['formalizing', 'failed', 'issued'], true))
                <form method="POST" action="{{ route('admin.invoices.refresh', $invoice) }}">
                    @csrf
                    <button class="px-4 py-2 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">Durumu yenile</button>
                </form>
            @endif
            @if($invoice->pdf_path)
                <a href="{{ route('admin.invoices.pdf', $invoice) }}" target="_blank" class="px-4 py-2 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">PDF</a>
            @endif
        </div>
    </div>

    @if($invoice->status === 'failed' && $invoice->error)
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">{{ $invoice->error }}</div>
    @endif
    @if($invoice->isDraft())
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg px-4 py-3 mb-6">
            Bu fatura Paraşüt'te <b>taslak</b>; henüz GİB'e gönderilmedi. İçerik yanlışsa taslağı silip yeniden oluşturabilir ya da Paraşüt panelinden düzeltebilirsiniz.
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-5">
            <div class="flex flex-wrap justify-between gap-4 mb-5">
                <div>
                    <p class="font-medium text-gray-900">{{ $invoice->contact_name }}</p>
                    <p class="text-xs text-gray-500">{{ $invoice->contact_type === 'company' ? 'VKN' : 'TC' }} {{ $invoice->contact_tax_number ?: '—' }} @if($invoice->party_label) · {{ $invoice->party_label }} @endif</p>
                </div>
                @if($invoice->description)<p class="text-xs text-gray-500">{{ $invoice->description }}</p>@endif
            </div>
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-500 uppercase border-b">
                    <tr><th class="text-left pb-2">Hizmet / ürün</th><th class="text-right pb-2">Miktar</th><th class="text-right pb-2">Br. fiyat</th><th class="text-right pb-2">KDV</th><th class="text-right pb-2">Toplam</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($invoice->lines ?? [] as $l)
                        <tr>
                            <td class="py-2">{{ $l['name'] }}</td>
                            <td class="py-2 text-right">{{ (float) $l['quantity'] }}</td>
                            <td class="py-2 text-right whitespace-nowrap">{{ number_format($l['unit_price'], 2, ',', '.') }}</td>
                            <td class="py-2 text-right">%{{ (float) $l['vat_rate'] }}</td>
                            <td class="py-2 text-right whitespace-nowrap">{{ number_format($l['total'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4 ml-auto max-w-xs text-sm space-y-1">
                <div class="flex justify-between text-gray-500"><span>Ara toplam</span><span>{{ number_format($invoice->net_total, 2, ',', '.') }} TL</span></div>
                <div class="flex justify-between text-gray-500"><span>KDV</span><span>{{ number_format($invoice->vat_total, 2, ',', '.') }} TL</span></div>
                <div class="flex justify-between font-semibold border-t pt-1"><span>Genel toplam</span><span>{{ number_format($invoice->total, 2, ',', '.') }} TL</span></div>
                @if($invoice->payment_recorded)<p class="text-xs text-green-700 text-right">Tahsilat işlendi</p>@endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Siparişler</h2>
            @forelse($invoice->orders as $o)
                <a href="{{ route('admin.orders.show', $o) }}" class="block py-2 {{ !$loop->first ? 'border-t border-gray-100' : '' }} hover:text-primary">
                    <p class="text-sm">#{{ $o->id }} · {{ $o->customer_name }}</p>
                    <p class="text-xs text-gray-500">{{ number_format($o->total_tl, 0, ',', '.') }} TL · {{ $o->status_label }}</p>
                </a>
            @empty
                <p class="text-sm text-gray-400">Bağlı sipariş yok.</p>
            @endforelse
            @if($invoice->match_note)<p class="text-xs text-gray-400 mt-3">{{ $invoice->match_note }}</p>@endif
            @if($invoice->parasut_id)
                <p class="text-xs text-gray-400 mt-3">Paraşüt #{{ $invoice->parasut_id }}</p>
            @endif
        </div>
    </div>
</x-admin.layouts.app>
