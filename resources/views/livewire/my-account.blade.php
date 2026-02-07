<div>
    {{-- Hero Header --}}
    <section class="bg-brand-black100 pt-12 pb-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                {{-- Avatar --}}
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary to-amber-400 flex items-center justify-center flex-shrink-0 shadow-lg">
                    <span class="text-3xl font-bold text-white">{{ mb_substr($user->name, 0, 1) }}</span>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl md:text-3xl font-light text-white">{{ $user->name }}</h1>
                    <p class="text-white/40 text-sm mt-1">{{ $user->email }}</p>
                    <div class="flex flex-wrap gap-4 mt-3">
                        <span class="text-xs text-white/50 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $user->created_at->format('d.m.Y') }} tarihinden beri
                        </span>
                        @if($user->phone)
                        <span class="text-xs text-white/50 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $user->phone }}
                        </span>
                        @endif
                        @if($stats['referrals_count'] > 0)
                        <span class="text-xs text-primary flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $stats['referrals_count'] }} referans
                        </span>
                        @endif
                    </div>
                </div>
                {{-- ArtPuan Badge --}}
                <div class="bg-white/5 border border-white/10 px-6 py-4 text-center">
                    <p class="text-[10px] text-white/40 uppercase tracking-widest mb-1">ArtPuan</p>
                    <p class="text-2xl font-bold text-primary">{{ number_format($stats['total_artpuan'], 0, ',', '.') }}</p>
                    <p class="text-[10px] text-white/30 mt-0.5">AP</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Tab Navigation --}}
    <div class="bg-white border-b border-gray-100 sticky top-[72px] z-30">
        <div class="container mx-auto px-4">
            <nav class="flex overflow-x-auto -mb-px scrollbar-hide">
                @php
                $tabs = [
                    'overview' => ['label' => 'Genel Bakış', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
                    'orders' => ['label' => 'Siparişlerim', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
                    'favorites' => ['label' => 'Favorilerim', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>'],
                    'artpuan' => ['label' => 'ArtPuan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                    'addresses' => ['label' => 'Adreslerim', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                    'settings' => ['label' => 'Ayarlar', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                ];
                @endphp

                @foreach($tabs as $key => $tab)
                    <button wire:click="setTab('{{ $key }}')"
                            class="flex items-center gap-2 px-5 py-4 text-sm whitespace-nowrap border-b-2 transition-colors
                                {{ $activeTab === $key
                                    ? 'border-brand-black100 text-brand-black100 font-medium'
                                    : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $tab['icon'] !!}</svg>
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 text-sm px-4 py-3 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ═══════════════════════════════════════════ --}}
        {{-- GENEL BAKIŞ --}}
        {{-- ═══════════════════════════════════════════ --}}
        @if($activeTab === 'overview')
            {{-- İstatistik Kartları --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                <div class="bg-white border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider">Siparişler</span>
                        <svg class="w-5 h-5 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                    </div>
                    <p class="text-2xl font-bold text-brand-black100">{{ $stats['total_orders'] }}</p>
                    @if($stats['pending_orders'] > 0)
                        <p class="text-[10px] text-amber-500 mt-1">{{ $stats['pending_orders'] }} beklemede</p>
                    @endif
                </div>
                <div class="bg-white border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider">Toplam Harcama</span>
                        <svg class="w-5 h-5 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-2xl font-bold text-brand-black100">{{ number_format($stats['total_spent'], 0, ',', '.') }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">TL</p>
                </div>
                <div class="bg-white border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider">Favoriler</span>
                        <svg class="w-5 h-5 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <p class="text-2xl font-bold text-brand-black100">{{ $stats['favorites_count'] }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">eser</p>
                </div>
                <div class="bg-white border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider">ArtPuan</span>
                        <svg class="w-5 h-5 text-primary/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
                    </div>
                    <p class="text-2xl font-bold text-primary">{{ number_format($stats['total_artpuan'], 0, ',', '.') }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">AP</p>
                </div>
            </div>

            {{-- Son Siparişler --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider">Son Siparişler</h2>
                    @if($stats['total_orders'] > 3)
                        <button wire:click="setTab('orders')" class="text-xs text-primary hover:underline">Tümünü Gör &rarr;</button>
                    @endif
                </div>
                @if($orders->count() > 0)
                    <div class="space-y-3">
                        @foreach($orders as $order)
                            <div class="bg-white border border-gray-100 p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-brand-black100">{{ $order->order_number }}</span>
                                        <span class="text-[10px] px-2 py-0.5 font-medium
                                            {{ $order->status === 'pending' ? 'bg-amber-50 text-amber-600' : '' }}
                                            {{ $order->status === 'paid' ? 'bg-green-50 text-green-600' : '' }}
                                            {{ $order->status === 'confirmed' ? 'bg-blue-50 text-blue-600' : '' }}
                                            {{ $order->status === 'shipped' ? 'bg-purple-50 text-purple-600' : '' }}
                                            {{ $order->status === 'delivered' ? 'bg-green-50 text-green-600' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-50 text-red-600' : '' }}
                                            {{ $order->status === 'payment_failed' ? 'bg-red-50 text-red-600' : '' }}
                                        ">{{ $order->status_label }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400">{{ $order->created_at->format('d.m.Y H:i') }} &middot; {{ $order->items->count() }} eser</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-brand-black100">{{ number_format($order->total_tl, 0, ',', '.') }} TL</p>
                                    <p class="text-[10px] text-gray-400">{{ $order->payment_method_label }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 text-center py-12">
                        <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                        <p class="text-sm text-gray-400">Henüz siparişiniz yok.</p>
                        <a href="{{ route('artworks') }}" class="inline-block mt-3 text-xs text-primary hover:underline">Eserleri Keşfet &rarr;</a>
                    </div>
                @endif
            </div>

            {{-- Son Favoriler --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider">Favorilerim</h2>
                    @if($stats['favorites_count'] > 4)
                        <button wire:click="setTab('favorites')" class="text-xs text-primary hover:underline">Tümünü Gör &rarr;</button>
                    @endif
                </div>
                @if($favorites->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($favorites as $fav)
                            @if($fav->artwork)
                            <a href="{{ route('artwork.detail', $fav->artwork->slug) }}" class="group bg-white border border-gray-100 overflow-hidden hover:shadow-md transition">
                                <div class="aspect-square overflow-hidden bg-gray-50">
                                    @if($fav->artwork->first_image_url)
                                        <img src="{{ $fav->artwork->first_image_url }}" alt="{{ $fav->artwork->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <p class="text-xs font-medium text-brand-black100 truncate">{{ $fav->artwork->title }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $fav->artwork->artist->name ?? '' }}</p>
                                    <p class="text-xs font-medium text-brand-black100 mt-1">{{ $fav->artwork->formatted_price_tl }}</p>
                                </div>
                            </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 text-center py-12">
                        <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <p class="text-sm text-gray-400">Favori eseriniz yok.</p>
                        <a href="{{ route('artworks') }}" class="inline-block mt-3 text-xs text-primary hover:underline">Eserleri Keşfet &rarr;</a>
                    </div>
                @endif
            </div>

            {{-- Son ArtPuan Hareketleri --}}
            @if($artPuanLogs->count() > 0)
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider">ArtPuan Hareketleri</h2>
                    <button wire:click="setTab('artpuan')" class="text-xs text-primary hover:underline">Tümünü Gör &rarr;</button>
                </div>
                <div class="bg-white border border-gray-100 divide-y divide-gray-50">
                    @foreach($artPuanLogs as $log)
                        <div class="flex items-center justify-between px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium {{ $log->type_color }}">{{ $log->type_label }}</span>
                                <div>
                                    <p class="text-xs text-gray-600">{{ $log->description ?? '-' }}</p>
                                    <p class="text-[10px] text-gray-300 mt-0.5">{{ $log->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold {{ $log->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $log->formatted_amount }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Referans Bölümü --}}
            <div class="bg-gradient-to-r from-brand-black100 to-gray-800 p-6 md:p-8">
                <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                    <div class="flex-1">
                        <h3 class="text-white font-semibold mb-1">Arkadaşını Davet Et, ArtPuan Kazan!</h3>
                        <p class="text-white/50 text-xs">Referans linkini paylaş, arkadaşların eser aldığında sen de %1 ArtPuan kazan.</p>
                    </div>
                    <div class="flex-shrink-0 w-full md:w-auto">
                        <div x-data="{ copied: false }" class="flex items-center gap-2">
                            <input type="text" value="{{ $user->referral_link }}" readonly class="bg-white/10 text-white/80 text-xs px-4 py-2.5 border border-white/10 w-full md:w-72 focus:outline-none">
                            <button @click="navigator.clipboard.writeText('{{ $user->referral_link }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="bg-primary hover:bg-primary-dark text-white px-4 py-2.5 text-xs font-medium transition flex-shrink-0">
                                <span x-show="!copied">Kopyala</span>
                                <span x-show="copied" x-cloak>Kopyalandı!</span>
                            </button>
                        </div>
                        <p class="text-white/30 text-[10px] mt-2">Referans kodunuz: {{ $user->referral_code }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════ --}}
        {{-- SİPARİŞLERİM --}}
        {{-- ═══════════════════════════════════════════ --}}
        @if($activeTab === 'orders')
            <h2 class="text-lg font-semibold text-brand-black100 mb-6">Siparişlerim</h2>
            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white border border-gray-100 overflow-hidden">
                            <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-semibold text-brand-black100">{{ $order->order_number }}</span>
                                        <span class="text-[10px] px-2 py-0.5 font-medium
                                            {{ $order->status === 'pending' ? 'bg-amber-50 text-amber-600' : '' }}
                                            {{ $order->status === 'paid' ? 'bg-green-50 text-green-600' : '' }}
                                            {{ $order->status === 'confirmed' ? 'bg-blue-50 text-blue-600' : '' }}
                                            {{ $order->status === 'shipped' ? 'bg-purple-50 text-purple-600' : '' }}
                                            {{ $order->status === 'delivered' ? 'bg-green-50 text-green-600' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-50 text-red-600' : '' }}
                                            {{ $order->status === 'payment_failed' ? 'bg-red-50 text-red-600' : '' }}
                                        ">{{ $order->status_label }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400">{{ $order->created_at->format('d.m.Y H:i') }} &middot; {{ $order->payment_method_label }}</p>
                                    @if($order->payment_code && $order->status === 'pending')
                                        <p class="text-xs text-amber-600 mt-1">Havale kodu: <strong class="font-mono">{{ $order->payment_code }}</strong></p>
                                    @endif
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-lg font-bold text-brand-black100">{{ number_format($order->total_tl, 0, ',', '.') }} TL</p>
                                    <p class="text-[10px] text-gray-400">{{ number_format($order->total_usd, 0, ',', '.') }} $</p>
                                </div>
                            </div>
                            {{-- Sipariş ürünleri --}}
                            <div class="border-t border-gray-50 px-5 py-3 bg-gray-50/50">
                                <div class="flex flex-wrap gap-3">
                                    @foreach($order->items as $item)
                                        <div class="flex items-center gap-2">
                                            @if($item->artwork && $item->artwork->first_image_url)
                                                <img src="{{ $item->artwork->first_image_url }}" alt="" class="w-10 h-10 object-cover border border-gray-100">
                                            @else
                                                <div class="w-10 h-10 bg-gray-200 flex items-center justify-center border border-gray-100">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16"/></svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-xs font-medium text-brand-black100">{{ $item->artwork_title }}</p>
                                                <p class="text-[10px] text-gray-400">{{ number_format($item->price_tl, 0, ',', '.') }} TL</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-gray-50">
                    <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                    <p class="text-gray-400 mb-2">Henüz siparişiniz bulunmuyor.</p>
                    <a href="{{ route('artworks') }}" class="inline-flex items-center gap-1.5 text-sm text-primary hover:underline">Eserleri Keşfet <span>&rarr;</span></a>
                </div>
            @endif
        @endif

        {{-- ═══════════════════════════════════════════ --}}
        {{-- FAVORİLERİM --}}
        {{-- ═══════════════════════════════════════════ --}}
        @if($activeTab === 'favorites')
            <h2 class="text-lg font-semibold text-brand-black100 mb-6">Favorilerim</h2>
            @if($favorites->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($favorites as $fav)
                        @if($fav->artwork)
                        <a href="{{ route('artwork.detail', $fav->artwork->slug) }}" class="group bg-white border border-gray-100 overflow-hidden hover:shadow-md transition">
                            <div class="aspect-square overflow-hidden bg-gray-50 relative">
                                @if($fav->artwork->first_image_url)
                                    <img src="{{ $fav->artwork->first_image_url }}" alt="{{ $fav->artwork->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @endif
                                @if($fav->artwork->is_sold)
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                        <span class="text-white text-xs font-medium bg-red-500/80 px-3 py-1">Satıldı</span>
                                    </div>
                                @elseif($fav->artwork->is_reserved)
                                    <div class="absolute top-2 left-2">
                                        <span class="text-[10px] bg-amber-500 text-white px-2 py-0.5 font-medium">Rezerve</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <p class="text-xs font-medium text-brand-black100 truncate">{{ $fav->artwork->title }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $fav->artwork->artist->name ?? '' }}</p>
                                <p class="text-xs font-semibold text-brand-black100 mt-1.5">{{ $fav->artwork->formatted_price_tl }}</p>
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-gray-50">
                    <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <p class="text-gray-400 mb-2">Favori eseriniz yok.</p>
                    <a href="{{ route('artworks') }}" class="inline-flex items-center gap-1.5 text-sm text-primary hover:underline">Eserleri Keşfet <span>&rarr;</span></a>
                </div>
            @endif
        @endif

        {{-- ═══════════════════════════════════════════ --}}
        {{-- ARTPUAN --}}
        {{-- ═══════════════════════════════════════════ --}}
        @if($activeTab === 'artpuan')
            {{-- ArtPuan Özet --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-gradient-to-br from-primary/10 to-amber-50 border border-primary/20 p-6 text-center">
                    <p class="text-xs text-primary/60 uppercase tracking-wider mb-2">Toplam ArtPuan</p>
                    <p class="text-4xl font-bold text-primary">{{ number_format($stats['total_artpuan'], 0, ',', '.') }}</p>
                    <p class="text-xs text-primary/40 mt-1">AP</p>
                </div>
                <div class="bg-white border border-gray-100 p-6 text-center">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Referanslarım</p>
                    <p class="text-4xl font-bold text-brand-black100">{{ $stats['referrals_count'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">kişi</p>
                </div>
                <div class="bg-white border border-gray-100 p-6">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Referans Linkin</p>
                    <div x-data="{ copied: false }" class="flex items-center gap-2">
                        <input type="text" value="{{ $user->referral_link }}" readonly class="flex-1 bg-gray-50 text-xs px-3 py-2 border border-gray-200 focus:outline-none truncate">
                        <button @click="navigator.clipboard.writeText('{{ $user->referral_link }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="bg-brand-black100 text-white px-3 py-2 text-xs hover:bg-black transition flex-shrink-0">
                            <span x-show="!copied">Kopyala</span>
                            <span x-show="copied" x-cloak>OK!</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Arkadaşını Davet Et & Referans Kodu Bağla --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                {{-- Referans Kodu Bağlama (sadece henüz bağlanmamışsa) --}}
                @if(!$user->referred_by)
                <div class="bg-white border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-brand-black100">Arkadaşının Hesabına Bağlan</h4>
                            <p class="text-[11px] text-gray-400">Seni davet eden arkadaşının referans kodunu gir</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Bir arkadaşın seni BeArtShare'e davet ettiyse, referans kodunu aşağıya girerek hesabınızı bağlayabilirsiniz. Bağlandıktan sonra yaptığınız alışverişlerde her ikiniz de <strong class="text-primary">%1 ArtPuan</strong> kazanırsınız!</p>
                    <div class="flex items-start gap-2">
                        <div class="flex-1">
                            <input type="text" wire:model="referral_code_input" placeholder="Referans kodunu gir..."
                                   class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('referral_code_input') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                            @error('referral_code_input') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button wire:click="bindReferralCode" wire:loading.attr="disabled"
                                class="bg-brand-black100 hover:bg-black text-white px-5 py-2.5 text-sm font-medium transition flex-shrink-0 disabled:opacity-50">
                            <span wire:loading.remove wire:target="bindReferralCode">Bağlan</span>
                            <span wire:loading wire:target="bindReferralCode">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                    </div>
                </div>
                @else
                {{-- Zaten bağlanmışsa referans bilgisi --}}
                <div class="bg-white border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-green-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-brand-black100">Referans Bağlantısı Aktif</h4>
                            <p class="text-[11px] text-gray-400">Bir arkadaşın seni davet etti</p>
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-100 p-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-green-600">{{ mb_substr($user->referrer->name ?? '?', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-800">{{ $user->referrer->name ?? '-' }}</p>
                            <p class="text-[10px] text-green-600">Seni davet eden arkadaşın &middot; Alışverişlerinde her ikiniz %1 AP kazanıyorsunuz</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Davet Et & Paylaş --}}
                <div class="bg-white border border-gray-100 p-6" x-data="{ copied: false }">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-brand-black100">Arkadaşlarını Davet Et</h4>
                            <p class="text-[11px] text-gray-400">Referans linkinle arkadaşlarını BeArtShare'e davet et</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Referans linkini paylaş, arkadaşların üye olup eser satın aldığında <strong class="text-primary">%1 ArtPuan</strong> kazan! Kodun: <strong class="font-mono text-primary">{{ $user->referral_code }}</strong></p>

                    {{-- Referans linki kopyalama --}}
                    <div class="flex items-center gap-2 mb-4">
                        <input type="text" value="{{ $user->referral_link }}" readonly class="flex-1 bg-gray-50 text-xs px-3 py-2.5 border border-gray-200 focus:outline-none truncate">
                        <button @click="navigator.clipboard.writeText('{{ $user->referral_link }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="bg-brand-black100 text-white px-4 py-2.5 text-xs hover:bg-black transition flex-shrink-0">
                            <span x-show="!copied">Kopyala</span>
                            <span x-show="copied" x-cloak>Kopyalandı!</span>
                        </button>
                    </div>

                    {{-- Sosyal medya paylaşım butonları --}}
                    <div class="flex flex-wrap gap-2">
                        {{-- WhatsApp --}}
                        <a href="https://wa.me/?text={{ urlencode('BeArtShare\'de harika eserler keşfet! Referans linkim ile üye ol ve alışverişlerinde ArtPuan kazan 🎨 ' . $user->referral_link) }}"
                           target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 bg-[#25D366] hover:bg-[#20bd5a] text-white px-3 py-2 text-xs font-medium transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                        {{-- E-posta --}}
                        <a href="mailto:?subject={{ urlencode('BeArtShare\'e Davetlisiniz!') }}&body={{ urlencode('Merhaba! BeArtShare\'de harika sanat eserlerini keşfetmeni istiyorum. Referans linkim ile üye ol ve alışverişlerinde ArtPuan kazan: ' . $user->referral_link) }}"
                           class="inline-flex items-center gap-1.5 bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 text-xs font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            E-posta
                        </a>
                        {{-- Twitter/X --}}
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode('BeArtShare\'de harika sanat eserlerini keşfet! 🎨 Referans linkim ile üye ol: ' . $user->referral_link) }}"
                           target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 bg-black hover:bg-gray-800 text-white px-3 py-2 text-xs font-medium transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            X
                        </a>
                        {{-- Facebook --}}
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($user->referral_link) }}"
                           target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 bg-[#1877F2] hover:bg-[#166fe5] text-white px-3 py-2 text-xs font-medium transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>
                        {{-- Telegram --}}
                        <a href="https://t.me/share/url?url={{ urlencode($user->referral_link) }}&text={{ urlencode('BeArtShare\'de harika sanat eserlerini keşfet! 🎨') }}"
                           target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 bg-[#0088cc] hover:bg-[#0077b5] text-white px-3 py-2 text-xs font-medium transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                            Telegram
                        </a>
                    </div>
                </div>
            </div>

            {{-- ArtPuan Hareket Tablosu --}}
            <h3 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider mb-4">Puan Hareketleri</h3>
            @if($artPuanLogs->count() > 0)
                <div class="bg-white border border-gray-100 divide-y divide-gray-50">
                    @foreach($artPuanLogs as $log)
                        <div class="flex items-center justify-between px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 flex items-center justify-center {{ $log->type_color }} flex-shrink-0">
                                    @if($log->type === 'purchase')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    @elseif($log->type === 'referral')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm text-brand-black100">{{ $log->description ?? $log->type_label }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] text-gray-300">{{ $log->created_at->format('d.m.Y H:i') }}</span>
                                        @if($log->order)
                                            <span class="text-[10px] text-gray-300">&middot; {{ $log->order->order_number }}</span>
                                        @endif
                                        @if($log->sourceUser)
                                            <span class="text-[10px] text-blue-400">&middot; Ref: {{ $log->sourceUser->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-bold {{ $log->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $log->formatted_amount }}</p>
                                <p class="text-[10px] text-gray-300">Bakiye: {{ number_format($log->balance_after, 0, ',', '.') }} AP</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gray-50">
                    <p class="text-sm text-gray-400">Henüz ArtPuan hareketiniz yok.</p>
                    <p class="text-xs text-gray-300 mt-1">Eser satın alarak veya arkadaşlarınızı davet ederek ArtPuan kazanın!</p>
                </div>
            @endif
        @endif

        {{-- ═══════════════════════════════════════════ --}}
        {{-- ADRESLERİM --}}
        {{-- ═══════════════════════════════════════════ --}}
        @if($activeTab === 'addresses')
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-brand-black100">Adreslerim</h2>
                <a href="{{ route('addresses') }}" class="text-xs bg-brand-black100 text-white px-4 py-2 hover:bg-black transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Adres Yönet
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Teslimat Adresleri --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        Teslimat Adresleri
                    </h3>
                    @forelse($shippingAddresses as $addr)
                        <div class="bg-white border border-gray-100 p-4 mb-3 {{ $addr->is_default ? 'border-l-2 border-l-primary' : '' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-brand-black100">{{ $addr->title ?: 'Teslimat Adresi' }}</span>
                                @if($addr->is_default)
                                    <span class="text-[10px] bg-primary/10 text-primary px-1.5 py-0.5">Varsayılan</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">{{ $addr->full_name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $addr->address_line }}, {{ $addr->district }}/{{ $addr->city }}</p>
                            @if($addr->phone)<p class="text-xs text-gray-400 mt-0.5">{{ $addr->phone }}</p>@endif
                        </div>
                    @empty
                        <div class="bg-gray-50 p-6 text-center text-sm text-gray-400">Teslimat adresi yok.</div>
                    @endforelse
                </div>

                {{-- Fatura Adresleri --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Fatura Adresleri
                    </h3>
                    @forelse($billingAddresses as $addr)
                        <div class="bg-white border border-gray-100 p-4 mb-3 {{ $addr->is_default ? 'border-l-2 border-l-primary' : '' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-brand-black100">{{ $addr->title ?: 'Fatura Adresi' }}</span>
                                @if($addr->is_default)
                                    <span class="text-[10px] bg-primary/10 text-primary px-1.5 py-0.5">Varsayılan</span>
                                @endif
                                <span class="text-[10px] px-1.5 py-0.5 {{ $addr->invoice_type === 'corporate' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $addr->invoice_type === 'corporate' ? 'Kurumsal' : 'Bireysel' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $addr->full_name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $addr->address_line }}, {{ $addr->district }}/{{ $addr->city }}</p>
                            @if($addr->invoice_type === 'corporate' && $addr->company_name)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $addr->company_name }} | {{ $addr->tax_office }} V.D. {{ $addr->tax_number }}</p>
                            @elseif($addr->tc_no)
                                <p class="text-xs text-gray-400 mt-0.5">TC: {{ $addr->tc_no }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="bg-gray-50 p-6 text-center text-sm text-gray-400">Fatura adresi yok.</div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════ --}}
        {{-- AYARLAR --}}
        {{-- ═══════════════════════════════════════════ --}}
        @if($activeTab === 'settings')
            <h2 class="text-lg font-semibold text-brand-black100 mb-6">Hesap Ayarları</h2>

            <div class="space-y-6 max-w-2xl">
                {{-- Profil Bilgileri --}}
                <div class="bg-white border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider mb-4">Profil Bilgileri</h3>

                    <dl class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-xs text-gray-400">Ad Soyad</dt>
                            <dd class="text-sm font-medium text-brand-black100">{{ $user->name }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-xs text-gray-400">E-posta</dt>
                            <dd class="text-sm font-medium text-brand-black100">{{ $user->email }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-xs text-gray-400">Telefon</dt>
                            <dd class="text-sm font-medium text-brand-black100">+90 {{ $user->phone ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-xs text-gray-400">TC Kimlik No</dt>
                            <dd class="text-sm font-medium text-brand-black100">{{ $user->tc_no ? substr($user->tc_no, 0, 3) . '****' . substr($user->tc_no, -2) : '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2">
                            <dt class="text-xs text-gray-400">Üyelik Tarihi</dt>
                            <dd class="text-sm font-medium text-brand-black100">{{ $user->created_at->format('d.m.Y') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 flex items-start gap-2">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Güvenlik nedeniyle bu bilgiler değiştirilemez. Bilgilerinizde hata varsa lütfen <a href="{{ route('contact') }}" class="text-primary hover:underline">bizimle iletişime</a> geçin.</span>
                        </p>
                    </div>
                </div>

                {{-- Şifre Değiştir --}}
                <div class="bg-white border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider">Şifre Değiştir</h3>
                        @if(!$showChangePassword)
                            <button wire:click="openChangePassword" class="text-xs text-primary hover:underline">Değiştir</button>
                        @endif
                    </div>

                    @if($showChangePassword)
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">Mevcut Şifre *</label>
                                <input type="password" wire:model="current_password"
                                       class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('current_password') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                                @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">Yeni Şifre *</label>
                                <input type="password" wire:model="new_password"
                                       class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('new_password') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                                @error('new_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">Yeni Şifre (Tekrar) *</label>
                                <input type="password" wire:model="new_password_confirmation"
                                       class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition">
                            </div>
                            <div class="flex gap-3">
                                <button wire:click="changePassword" class="bg-brand-black100 hover:bg-black text-white px-6 py-2.5 text-sm font-medium transition">Şifreyi Güncelle</button>
                                <button wire:click="$set('showChangePassword', false)" class="border border-gray-200 px-6 py-2.5 text-sm text-gray-500 hover:bg-gray-50 transition">İptal</button>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-400">Güvenliğiniz için şifrenizi düzenli olarak değiştirmenizi öneririz.</p>
                    @endif
                </div>

                {{-- Referans Kodu --}}
                <div class="bg-white border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider mb-4">Referans Bilgileri</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-xs text-gray-400">Referans Kodunuz</dt>
                            <dd class="text-sm font-mono font-medium text-primary">{{ $user->referral_code }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-xs text-gray-400">Toplam Referans</dt>
                            <dd class="text-sm font-medium text-brand-black100">{{ $stats['referrals_count'] }} kişi</dd>
                        </div>
                        @if($user->referrer)
                        <div class="flex justify-between py-2">
                            <dt class="text-xs text-gray-400">Sizi Davet Eden</dt>
                            <dd class="text-sm font-medium text-brand-black100">{{ $user->referrer->name }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        @endif
    </div>
</div>
