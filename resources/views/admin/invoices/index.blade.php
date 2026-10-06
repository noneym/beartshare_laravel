<x-admin.layouts.app>
    <x-slot name="title">Faturalar</x-slot>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Faturalar</h1>
            <p class="text-sm text-gray-500 mt-1">
                Paraşüt satış faturaları
                @if($stats['last_sync']) · son aktarım {{ \Carbon\Carbon::parse($stats['last_sync'])->format('d.m.Y H:i') }} @endif
            </p>
        </div>
        <a href="{{ route('admin.invoices.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg text-sm hover:opacity-90">+ Yeni fatura</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Fatura</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['count']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Toplam (KDV dahil)</p>
            <p class="text-xl font-bold text-gray-800 mt-1">{{ number_format($stats['total'], 0, ',', '.') }} TL</p>
        </div>
        <a href="{{ route('admin.invoices.index', ['match' => 'none']) }}" class="bg-white rounded-lg shadow p-4 hover:ring-1 hover:ring-red-200">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Siparişsiz</p>
            <p class="text-2xl font-bold {{ $stats['unmatched'] ? 'text-red-600' : 'text-gray-800' }} mt-1">{{ $stats['unmatched'] }}</p>
        </a>
        <a href="{{ route('admin.invoices.index', ['match' => 'review']) }}" class="bg-white rounded-lg shadow p-4 hover:ring-1 hover:ring-amber-200">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Kontrol edilmeli</p>
            <p class="text-2xl font-bold {{ $stats['review'] ? 'text-amber-600' : 'text-gray-800' }} mt-1">{{ $stats['review'] }}</p>
            <p class="text-[11px] text-gray-400">TC / eser ile tahmini eşleşme</p>
        </a>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">PDF eksik</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['no_pdf'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs text-gray-500 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Fatura no, kişi, TC/VKN, eser, sipariş no"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Eşleşme</label>
                <select name="match" class="border border-gray-300 rounded px-3 py-2 text-sm bg-white">
                    <option value="">Tümü</option>
                    <option value="none" @selected(request('match') === 'none')>Siparişsiz</option>
                    <option value="review" @selected(request('match') === 'review')>Kontrol edilmeli</option>
                    @foreach(\App\Models\Invoice::MATCH_METHODS as $k => $label)
                        <option value="{{ $k }}" @selected(request('match') === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Taraf</label>
                <select name="party" class="border border-gray-300 rounded px-3 py-2 text-sm bg-white">
                    <option value="">Tümü</option>
                    <option value="buyer" @selected(request('party') === 'buyer')>Alıcı</option>
                    <option value="seller" @selected(request('party') === 'seller')>Satıcı</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Yıl</label>
                <select name="year" class="border border-gray-300 rounded px-3 py-2 text-sm bg-white">
                    <option value="">Tümü</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected((string) request('year') === (string) $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button class="px-4 py-2 bg-gray-800 text-white rounded text-sm">Filtrele</button>
            @if(request()->hasAny(['search', 'match', 'party', 'year']))
                <a href="{{ route('admin.invoices.index') }}" class="text-sm text-gray-500 hover:underline py-2">Temizle</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <x-admin.th sort="no" first="desc" class="px-4 py-3 text-left">Fatura</x-admin.th>
                        <x-admin.th sort="contact" first="asc" class="px-4 py-3 text-left">Kişi</x-admin.th>
                        <th class="px-4 py-3 text-left">Kalemler</th>
                        <x-admin.th sort="total" first="desc" class="px-4 py-3 text-right">Tutar</x-admin.th>
                        <x-admin.th sort="match" first="asc" class="px-4 py-3 text-left">Sipariş</x-admin.th>
                        <th class="px-4 py-3 text-right">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $inv)
                        <tr class="align-top hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.invoices.show', $inv) }}" class="font-mono text-gray-900 hover:text-primary">{{ $inv->invoice_no ?: 'Taslak' }}</a>
                                @if($inv->status !== 'issued')<span class="ml-1 text-[11px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">{{ $inv->status_label }}</span>@endif
                                <p class="text-xs text-gray-500">{{ $inv->issue_date->format('d.m.Y') }} · {{ $inv->e_document_label ?? 'belge yok' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-gray-900">{{ $inv->contact_name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $inv->contact_tax_number ?: '—' }}
                                    @if($inv->party_label)
                                        · <span class="{{ $inv->party === 'seller' ? 'text-purple-700' : 'text-blue-700' }}">{{ $inv->party_label }}</span>
                                    @endif
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 max-w-md">
                                @foreach($inv->lines ?? [] as $line)
                                    <p class="truncate" title="{{ $line['name'] }}">{{ $line['quantity'] > 1 ? (float) $line['quantity'] . ' × ' : '' }}{{ $line['name'] }}</p>
                                @endforeach
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <p class="font-medium text-gray-900">{{ number_format($inv->total, 2, ',', '.') }} TL</p>
                                <p class="text-xs text-gray-500">KDV {{ number_format($inv->vat_total, 2, ',', '.') }}</p>
                            </td>
                            <td class="px-4 py-3" x-data="{ edit: false }">
                                <div class="flex flex-wrap gap-1.5 items-center">
                                    @forelse($inv->orders as $o)
                                        <a href="{{ route('admin.orders.show', $o->id) }}" class="px-2 py-0.5 bg-gray-100 rounded text-xs text-gray-800 hover:bg-gray-200" title="{{ $o->customer_name }}">#{{ $o->id }}</a>
                                    @empty
                                        <span class="px-2 py-0.5 bg-red-50 text-red-700 rounded text-xs">Siparişsiz</span>
                                    @endforelse
                                    <button type="button" @click="edit = !edit" class="text-xs text-gray-400 hover:text-gray-700" title="Siparişi elle bağla">düzenle</button>
                                </div>
                                @if($inv->match_method)
                                    <p class="text-[11px] mt-1 {{ $inv->isFuzzyMatch() ? 'text-amber-700' : 'text-gray-400' }}">
                                        {{ \App\Models\Invoice::MATCH_METHODS[$inv->match_method] ?? $inv->match_method }}{{ $inv->match_note && $inv->isFuzzyMatch() && str_contains($inv->match_note, ';') ? ' — ' . \Illuminate\Support\Str::after($inv->match_note, '; ') : '' }}
                                    </p>
                                    @if($inv->isFuzzyMatch())
                                        <form method="POST" action="{{ route('admin.invoices.confirm', $inv) }}" class="inline">
                                            @csrf
                                            <button class="text-[11px] text-green-700 hover:underline">Doğru, onayla</button>
                                        </form>
                                    @endif
                                @endif
                                <form x-show="edit" x-cloak method="POST" action="{{ route('admin.invoices.link', $inv) }}" class="mt-2 flex gap-1.5">
                                    @csrf
                                    <input type="text" name="order_ids" value="{{ $inv->orders->pluck('id')->implode(', ') }}" placeholder="190, 191"
                                           class="w-28 border border-gray-300 rounded px-2 py-1 text-xs">
                                    <button class="px-2 py-1 bg-gray-800 text-white rounded text-xs">Kaydet</button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if($inv->pdf_path)
                                    <a href="{{ route('admin.invoices.pdf', $inv) }}" target="_blank" class="text-primary hover:underline text-xs">PDF</a>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Fatura bulunamadı. İçe aktarmak için: <code>php artisan parasut:import-invoices --pdf</code></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $invoices->links() }}</div>
    </div>
</x-admin.layouts.app>
