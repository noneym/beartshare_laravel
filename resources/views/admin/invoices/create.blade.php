<x-admin.layouts.app>
    <x-slot name="title">Yeni Fatura</x-slot>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Yeni Fatura</h1>
            <p class="text-sm text-gray-500 mt-1">Paraşüt'te taslak olarak oluşturulur; kontrol ettikten sonra resmileştirirsiniz.</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="text-sm text-gray-500 hover:underline">← Faturalar</a>
    </div>

    {{-- Sipariş seçimi --}}
    <form method="GET" action="{{ route('admin.invoices.create') }}" class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sipariş(ler)</label>
            <input type="text" name="orders" value="{{ request('orders') }}" placeholder="190 ya da 193, 194"
                   class="w-56 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
        </div>
        <button class="px-4 py-2 bg-gray-800 text-white rounded text-sm">{{ $orders->isEmpty() ? 'Siparişten doldur' : 'Yeniden doldur' }}</button>
        <p class="text-xs text-gray-400">Birden fazla sipariş tek faturada toplanabilir. "1050190" biçimi de olur.</p>
    </form>

    @if($missing->isNotEmpty())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">Sipariş bulunamadı: #{{ $missing->implode(', #') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
    @endif

    @if($defaults)
        @php
            $existing = $orders->flatMap->invoices->unique('id');
            $v = fn ($key) => old($key, $defaults[$key] ?? null);
            $lines = old('lines', $defaults['lines']);
        @endphp

        @if($existing->isNotEmpty())
            <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg px-4 py-3 mb-6">
                Bu siparişlerin zaten faturası var:
                @foreach($existing as $ex)
                    <a href="{{ route('admin.invoices.show', $ex) }}" class="underline">{{ $ex->invoice_no ?: 'taslak #' . $ex->id }}</a> ({{ $ex->party_label ?? $ex->contact_name }}){{ !$loop->last ? ',' : '' }}
                @endforeach
                — yine de yeni fatura kesebilirsiniz (ör. satıcı komisyonu).
            </div>
        @endif

        <form method="POST" action="{{ route('admin.invoices.store') }}" class="space-y-6"
              x-data="{
                  lines: @js(array_values($lines)),
                  recordPayment: @js((bool) old('record_payment', $defaults['record_payment'])),
                  paymentAmount: @js((float) $v('payment_amount')),
                  paymentTouched: false,
                  lineTotal(l) { return (parseFloat(l.unit_price) || 0) * (parseFloat(l.quantity) || 0) * (1 + (parseFloat(l.vat_rate) || 0) / 100); },
                  get net() { return this.lines.reduce((s, l) => s + (parseFloat(l.unit_price) || 0) * (parseFloat(l.quantity) || 0), 0); },
                  get gross() { return this.lines.reduce((s, l) => s + this.lineTotal(l), 0); },
                  tl(v) { return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(v) + ' TL'; },
                  syncPayment() { if (!this.paymentTouched) this.paymentAmount = Math.round(this.gross * 100) / 100; },
              }"
              x-init="$watch('lines', () => syncPayment(), { deep: true })">
            @csrf
            <input type="hidden" name="order_ids" value="{{ $orders->pluck('id')->implode(',') }}">

            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($orders as $o)
                        <a href="{{ route('admin.orders.show', $o) }}" target="_blank" class="px-2 py-1 bg-gray-100 rounded text-xs text-gray-700 hover:bg-gray-200">
                            #{{ $o->id }} · {{ $o->customer_name }} · {{ number_format($o->total_tl, 0, ',', '.') }} TL · {{ $o->status_label }}
                        </a>
                    @endforeach
                </div>

                <h2 class="font-semibold text-gray-800 mb-3">Müşteri</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-data="{ type: @js($v('contact_type')) }">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tür</label>
                        <select name="contact_type" x-model="type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-white">
                            <option value="person">Bireysel (TC)</option>
                            <option value="company">Kurumsal (VKN)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1" x-text="type === 'company' ? 'Unvan *' : 'Ad Soyad *'">Ad Soyad *</label>
                        <input type="text" name="contact_name" value="{{ $v('contact_name') }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1" x-text="type === 'company' ? 'VKN *' : 'TC Kimlik No *'">TC Kimlik No *</label>
                        <input type="text" name="tax_number" value="{{ $v('tax_number') }}" required inputmode="numeric" maxlength="11" class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono">
                        @if($v('tax_number') === \App\Services\InvoiceService::ANONYMOUS_TC)
                            <p class="text-[11px] text-amber-700 mt-1">Siparişte TC yok; GİB'in kabul ettiği 11111111111 kullanılacak.</p>
                        @endif
                    </div>
                    <div x-show="type === 'company'">
                        <label class="block text-xs text-gray-500 mb-1">Vergi Dairesi *</label>
                        <input type="text" name="tax_office" value="{{ $v('tax_office') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">E-posta</label>
                        <input type="email" name="email" value="{{ $v('email') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Telefon</label>
                        <input type="text" name="phone" value="{{ $v('phone') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs text-gray-500 mb-1">Adres *</label>
                        <input type="text" name="address" value="{{ $v('address') }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">İlçe *</label>
                        <input type="text" name="district" value="{{ $v('district') }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">İl *</label>
                        <input type="text" name="city" value="{{ $v('city') }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Fatura tarihi *</label>
                        <input type="date" name="issue_date" value="{{ $v('issue_date') }}" min="{{ now()->subDays(7)->toDateString() }}" max="{{ now()->toDateString() }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <p class="text-[11px] text-gray-400 mt-1">e-Arşiv en fazla 7 gün geriye</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">Açıklama</label>
                        <input type="text" name="description" value="{{ $v('description') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>

                <h2 class="font-semibold text-gray-800 mb-3">Kalemler</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="text-left pb-2">Hizmet / ürün</th>
                                <th class="text-right pb-2 w-24">Miktar</th>
                                <th class="text-right pb-2 w-36">Br. fiyat (KDV hariç)</th>
                                <th class="text-right pb-2 w-24">KDV %</th>
                                <th class="text-right pb-2 w-36">Toplam</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(line, i) in lines" :key="i">
                                <tr class="align-top">
                                    <td class="pr-2 py-1">
                                        <input type="text" :name="`lines[${i}][name]`" x-model="line.name" required class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                        <p class="text-[11px] text-gray-400 mt-0.5" x-show="line.source" x-text="'Tutar kaynağı: ' + line.source"></p>
                                    </td>
                                    <td class="px-1 py-1"><input type="number" step="0.01" min="0.01" :name="`lines[${i}][quantity]`" x-model="line.quantity" required class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm text-right"></td>
                                    <td class="px-1 py-1"><input type="number" step="0.01" min="0" :name="`lines[${i}][unit_price]`" x-model="line.unit_price" required class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm text-right"></td>
                                    <td class="px-1 py-1">
                                        <select :name="`lines[${i}][vat_rate]`" x-model="line.vat_rate" class="w-full border border-gray-300 rounded px-1 py-1.5 text-sm bg-white">
                                            <option value="20">20</option><option value="10">10</option><option value="1">1</option><option value="0">0</option>
                                        </select>
                                    </td>
                                    <td class="pl-1 py-1 text-right whitespace-nowrap pt-2.5" x-text="tl(lineTotal(line))"></td>
                                    <td class="pl-2 py-1 pt-2">
                                        <button type="button" @click="lines.splice(i, 1)" x-show="lines.length > 1" class="text-gray-400 hover:text-red-600" aria-label="Satırı sil">✕</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <button type="button" @click="lines.push({ name: '', quantity: 1, unit_price: 0, vat_rate: 20 })" class="mt-2 text-xs text-primary hover:underline">+ Satır ekle</button>

                <div class="mt-4 ml-auto max-w-xs text-sm space-y-1">
                    <div class="flex justify-between text-gray-500"><span>Ara toplam</span><span x-text="tl(net)"></span></div>
                    <div class="flex justify-between text-gray-500"><span>KDV</span><span x-text="tl(gross - net)"></span></div>
                    <div class="flex justify-between font-semibold text-gray-900 border-t pt-1"><span>Genel toplam</span><span x-text="tl(gross)"></span></div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                    <input type="checkbox" name="record_payment" value="1" x-model="recordPayment" class="rounded border-gray-300 text-primary focus:ring-primary">
                    Tahsilatı da işle
                </label>
                <div x-show="recordPayment" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Kasa / banka hesabı</label>
                        <select name="payment_account_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-white">
                            @foreach($accounts as $id => $name)
                                <option value="{{ $id }}" @selected((string) $v('payment_account_id') === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @if(!$accounts)<p class="text-[11px] text-red-600 mt-1">Paraşüt hesap listesi alınamadı.</p>@endif
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tahsilat tarihi</label>
                        <input type="date" name="payment_date" value="{{ $v('payment_date') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tutar (KDV dahil)</label>
                        <input type="number" step="0.01" min="0.01" name="payment_amount" x-model="paymentAmount" @input="paymentTouched = true" class="w-full border border-gray-300 rounded px-3 py-2 text-sm text-right">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ url()->previous() }}" class="px-4 py-2 border border-gray-300 rounded text-sm text-gray-600">Vazgeç</a>
                <button class="px-5 py-2 bg-primary text-white rounded text-sm font-medium hover:opacity-90"
                        onclick="return confirm('Paraşüt\'te taslak fatura oluşturulsun mu? (Henüz resmileşmez)')">Paraşüt'te taslak oluştur</button>
            </div>
        </form>
    @endif
</x-admin.layouts.app>
