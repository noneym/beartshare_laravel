<x-admin.layouts.app>
    <x-slot name="title">Bildirim Loglari</x-slot>

<div x-data="{
    showModal: false,
    modalData: {
        channel: '',
        type: '',
        recipient: '',
        subject: '',
        message: '',
        error: '',
        api_response: '',
        created_at: '',
        user_name: '',
        order_number: '',
        order_url: ''
    },
    openModal(data) {
        this.modalData = data;
        this.showModal = true;
    }
}">

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
                    <option value="admin_sms" {{ request('type') === 'admin_sms' ? 'selected' : '' }}>Admin SMS</option>
                    <option value="admin_email" {{ request('type') === 'admin_email' ? 'selected' : '' }}>Admin E-posta</option>
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
                            <button type="button"
                                    @click="openModal({
                                        channel: '{{ $log->channel_label }}',
                                        type: '{{ $log->type_label }}',
                                        recipient: '{{ $log->recipient }}',
                                        subject: '{{ addslashes($log->subject ?? '') }}',
                                        message: `{{ addslashes($log->message ?? '') }}`,
                                        error: '{{ addslashes($log->error ?? '') }}',
                                        api_response: '{{ addslashes($log->api_response ?? '') }}',
                                        created_at: '{{ $log->created_at->format('d.m.Y H:i:s') }}',
                                        user_name: '{{ $log->user?->name ?? '' }}',
                                        order_number: '{{ $log->order?->order_number ?? '' }}',
                                        order_url: '{{ $log->order ? route('admin.orders.show', $log->order) : '' }}'
                                    })"
                                    class="text-left hover:bg-gray-100 p-1 -m-1 rounded transition w-full">
                                @if($log->subject)
                                    <p class="font-medium text-gray-700 truncate">{{ $log->subject }}</p>
                                @endif
                                <p class="truncate text-gray-400">{{ Str::limit($log->message, 80) }}</p>
                                @if($log->error)
                                    <p class="text-red-500 text-xs mt-1 truncate">
                                        Hata: {{ Str::limit($log->error, 60) }}
                                    </p>
                                @endif
                                <span class="text-primary text-[10px] mt-1 inline-block">Detay için tıklayın</span>
                            </button>
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

    {{-- Mesaj Detay Modal --}}
    <template x-if="showModal">
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showModal = false"></div>

            {{-- Modal Content --}}
            <div class="relative bg-white rounded-lg shadow-xl sm:max-w-2xl sm:w-full mx-4" @click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Bildirim Detayi</h3>
                            <p class="text-xs text-gray-500" x-text="modalData.created_at"></p>
                        </div>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-4 max-h-[60vh] overflow-y-auto">
                    {{-- Meta Bilgiler --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Kanal</p>
                            <p class="text-sm font-medium text-gray-900" x-text="modalData.channel"></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Tip</p>
                            <p class="text-sm font-medium text-gray-900" x-text="modalData.type"></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Alici</p>
                            <p class="text-sm font-medium text-gray-900" x-text="modalData.recipient"></p>
                            <p class="text-xs text-gray-400" x-show="modalData.user_name" x-text="modalData.user_name"></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3" x-show="modalData.order_number">
                            <p class="text-xs text-gray-500 mb-1">Siparis</p>
                            <a :href="modalData.order_url" class="text-sm font-medium text-primary hover:underline" x-text="'#' + modalData.order_number"></a>
                        </div>
                    </div>

                    {{-- Konu --}}
                    <div x-show="modalData.subject" class="mb-4">
                        <p class="text-xs text-gray-500 mb-1">Konu</p>
                        <p class="text-sm font-medium text-gray-900 bg-gray-50 rounded-lg p-3" x-text="modalData.subject"></p>
                    </div>

                    {{-- Mesaj Icerigi --}}
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-1">Mesaj Icerigi</p>
                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap break-words border border-gray-200" x-text="modalData.message"></div>
                    </div>

                    {{-- Hata --}}
                    <div x-show="modalData.error" class="mb-4">
                        <p class="text-xs text-red-500 mb-1">Hata Mesaji</p>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700" x-text="modalData.error"></div>
                    </div>

                    {{-- API Response --}}
                    <div x-show="modalData.api_response" class="mb-4">
                        <p class="text-xs text-gray-500 mb-1">API Yaniti</p>
                        <div class="bg-gray-100 rounded-lg p-3 text-xs font-mono text-gray-600 overflow-x-auto" x-text="modalData.api_response"></div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <button @click="showModal = false" class="w-full bg-gray-800 hover:bg-gray-900 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
    </template>

</div>
</x-admin.layouts.app>
