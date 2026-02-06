<x-admin.layouts.app title="Ödeme İşlemleri">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Ödeme İşlemleri</h1>
        <a href="{{ route('admin.payments.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Yeni Ödeme Ekle
        </a>
    </div>

    {{-- Bildirim Mesajları --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtreler --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form action="{{ route('admin.payments.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm text-gray-600 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="İşlem no, sipariş no, onay kodu..." class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div class="w-40">
                <label class="block text-sm text-gray-600 mb-1">Durum</label>
                <select name="status" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Tümü</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Başarılı</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Beklemede</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Başarısız</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>İade</option>
                </select>
            </div>
            <div class="w-40">
                <label class="block text-sm text-gray-600 mb-1">Yöntem</label>
                <select name="gateway" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Tümü</option>
                    <option value="garanti" {{ request('gateway') === 'garanti' ? 'selected' : '' }}>Garanti</option>
                    <option value="havale" {{ request('gateway') === 'havale' ? 'selected' : '' }}>Havale</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-1 text-sm text-gray-600">
                    <input type="checkbox" name="with_trashed" value="1" {{ request('with_trashed') ? 'checked' : '' }} class="rounded border-gray-300">
                    Silinenler
                </label>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Filtrele</button>
            @if(request()->hasAny(['search', 'status', 'gateway', 'with_trashed']))
                <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Temizle</a>
            @endif
        </form>
    </div>

    {{-- Tablo --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sipariş</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yöntem</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payments as $payment)
                    <tr class="{{ $payment->trashed() ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 text-sm text-gray-500">#{{ $payment->id }}</td>
                        <td class="px-6 py-4">
                            @if($payment->order)
                                <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-blue-600 hover:underline text-sm font-medium">
                                    {{ $payment->order->order_number }}
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $payment->order->user->name ?? $payment->order->customer_name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 text-xs rounded {{ $payment->gateway === 'garanti' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst($payment->gateway) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $payment->formatted_amount }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 text-xs rounded-full
                                {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $payment->status === 'refunded' ? 'bg-blue-100 text-blue-800' : '' }}
                            ">
                                {{ $payment->status_text }}
                            </span>
                            @if($payment->trashed())
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 ml-1">Silindi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detay</a>
                                @if($payment->trashed())
                                    <form action="{{ route('admin.payments.restore', $payment->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 text-sm">Geri Al</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Bu ödeme kaydını silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Sil</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            Ödeme kaydı bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $payments->links() }}
    </div>
</x-admin.layouts.app>
