<x-admin.layouts.app>
    <x-slot name="title">Bildirim Loglari</x-slot>

    {{-- Baslik --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bildirim Loglari</h1>
            <p class="text-sm text-gray-500 mt-1">Tum SMS ve e-posta bildirimleri</p>
        </div>
    </div>

    {{-- Istatistik Kartlari --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Toplam</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">SMS</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['sms_count']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">E-posta</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($stats['email_count']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Basarili</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['success_count']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Basarisiz</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['failed_count']) }}</p>
        </div>
    </div>

    {{-- Filtreler --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.notification-logs.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Alici, mesaj, kullanici..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs text-gray-500 mb-1">Kanal</label>
                <select name="channel" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">Tumu</option>
                    <option value="sms" {{ request('channel') === 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="email" {{ request('channel') === 'email' ? 'selected' : '' }}>E-posta</option>
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs text-gray-500 mb-1">Durum</label>
                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">Tumu</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Basarili</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Basarisiz</option>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs text-gray-500 mb-1">Tip</label>
                <select name="type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">Tumu</option>
                    <option value="order_created" {{ request('type') === 'order_created' ? 'selected' : '' }}>Siparis Olusturuldu</option>
                    <option value="buyer_artpuan" {{ request('type') === 'buyer_artpuan' ? 'selected' : '' }}>Alici ArtPuan</option>
                    <option value="referrer_artpuan" {{ request('type') === 'referrer_artpuan' ? 'selected' : '' }}>Referans ArtPuan</option>
                    <option value="favorite_reserved" {{ request('type') === 'favorite_reserved' ? 'selected' : '' }}>Favori Rezerve</option>
                    <option value="sms_verification" {{ request('type') === 'sms_verification' ? 'selected' : '' }}>SMS Dogrulama</option>
                    <option value="sms_verification_resend" {{ request('type') === 'sms_verification_resend' ? 'selected' : '' }}>SMS Dogrulama (Tekrar)</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700 transition">
                Filtrele
            </button>
            @if(request()->hasAny(['search', 'channel', 'status', 'type']))
                <a href="{{ route('admin.notification-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kanal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alici</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mesaj / Konu</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siparis</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                            {{ $log->created_at->format('d.m.Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->channel_color }}">
                                {{ $log->channel_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->type_color }}">
                                {{ $log->type_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div>
                                <p class="text-sm text-gray-900">{{ $log->recipient }}</p>
                                @if($log->user)
                                    <p class="text-xs text-gray-400">{{ $log->user->name }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 max-w-xs">
                            @if($log->subject)
                                <p class="font-medium text-gray-700 truncate">{{ $log->subject }}</p>
                            @endif
                            <p class="truncate text-gray-400" title="{{ $log->message }}">{{ Str::limit($log->message, 80) }}</p>
                            @if($log->error)
                                <p class="text-red-500 text-xs mt-1 truncate" title="{{ $log->error }}">
                                    Hata: {{ Str::limit($log->error, 60) }}
                                </p>
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
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->status_color }}">
                                {{ $log->status_label }}
                            </span>
                            @if($log->api_response)
                                <p class="text-[10px] text-gray-400 mt-0.5 font-mono">{{ Str::limit($log->api_response, 20) }}</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">
                            Henuz bildirim logu bulunmuyor.
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
