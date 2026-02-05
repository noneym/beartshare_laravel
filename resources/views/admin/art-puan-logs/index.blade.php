<x-admin.layouts.app>
    <x-slot name="title">ArtPuan Log</x-slot>

    {{-- Baslik --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">ArtPuan Log</h1>
            <p class="text-sm text-gray-500 mt-1">Tum ArtPuan hareketleri</p>
        </div>
    </div>

    {{-- Istatistik Kartlari --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Toplam Dagitilan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_distributed'], 2, ',', '.') }} AP</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Satin Alma Puani</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['purchase_total'], 2, ',', '.') }} AP</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Referans Puani</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['referral_total'], 2, ',', '.') }} AP</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Toplam Islem</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['log_count']) }}</p>
        </div>
    </div>

    {{-- Filtreler --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.art-puan-logs.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Kullanici Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad veya e-posta..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs text-gray-500 mb-1">Tip</label>
                <select name="type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">Tumu</option>
                    <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>Satin Alma</option>
                    <option value="referral" {{ request('type') === 'referral' ? 'selected' : '' }}>Referans</option>
                    <option value="bonus" {{ request('type') === 'bonus' ? 'selected' : '' }}>Bonus</option>
                    <option value="manual" {{ request('type') === 'manual' ? 'selected' : '' }}>Manuel</option>
                    <option value="refund" {{ request('type') === 'refund' ? 'selected' : '' }}>Iade</option>
                    <option value="spend" {{ request('type') === 'spend' ? 'selected' : '' }}>Harcama</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700 transition">
                Filtrele
            </button>
            @if(request('search') || request('type'))
                <a href="{{ route('admin.art-puan-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">
                    Temizle
                </a>
            @endif
        </form>
    </div>

    {{-- Tablo --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanici</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aciklama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siparis</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Miktar</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Bakiye</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                            {{ $log->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $log->user->name ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $log->user->email ?? '' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->type_color }}">
                                {{ $log->type_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate">
                            {{ $log->description ?? '-' }}
                            @if($log->sourceUser)
                                <br><span class="text-gray-400">Ref: {{ $log->sourceUser->name }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs">
                            @if($log->order)
                                <a href="{{ route('admin.orders.show', $log->order) }}" class="text-primary hover:underline">
                                    #{{ $log->order->order_number }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium {{ $log->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $log->formatted_amount }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500">
                            {{ number_format($log->balance_after, 2, ',', '.') }} AP
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">
                            Henuz ArtPuan hareketi bulunmuyor.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif
</x-admin.layouts.app>
