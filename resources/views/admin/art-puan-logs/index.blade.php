<x-admin.layouts.app>
    <x-slot name="title">ArtPuan Log</x-slot>

    {{-- Baslik --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">ArtPuan Log</h1>
            <p class="text-sm text-gray-500 mt-1">Tum ArtPuan hareketleri</p>
        </div>
        <a href="{{ route('admin.art-puan-logs.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Manuel ArtPuan Ekle
        </a>
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
            <div class="flex items-center">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="with_trashed" value="1" {{ request('with_trashed') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-gray-600">Silinmisleri Goster</span>
                </label>
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
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Islemler</th>
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
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($log->trashed())
                                <form action="{{ route('admin.art-puan-logs.restore', $log->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium" title="Geri Yukle">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                @if($log->amount > 0)
                                    <form action="{{ route('admin.art-puan-logs.destroy', $log) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Bu kaydi silmek ve {{ number_format($log->amount, 2, ',', '.') }} AP geri almak istediginize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium" title="Sil ve Puani Geri Al">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-400">
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
