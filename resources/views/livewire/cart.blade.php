<div>
    <!-- Page Header -->
    <section class="bg-brand-black100 py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-light text-white">Sepet<span class="font-semibold">im</span></h1>
        </div>
    </section>

    <div class="container mx-auto px-4 py-10">
        @if($cartItems->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cartItems as $item)
                        <div class="border border-gray-100 p-4 flex gap-4" wire:key="cart-{{ $item->id }}">
                            <a href="{{ route('artwork.detail', $item->artwork->slug) }}" class="w-28 h-28 bg-gray-50 overflow-hidden flex-shrink-0">
                                @if($item->artwork->first_image)
                                    <img src="{{ $item->artwork->first_image_url }}" alt="{{ $item->artwork->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </a>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-medium text-brand-black100 text-sm">{{ $item->artwork->artist->name }}</h3>
                                        <p class="text-gray-400 text-xs mt-0.5">{{ $item->artwork->title }}</p>
                                        <p class="text-gray-300 text-[10px] mt-0.5">{{ $item->artwork->dimensions }} - {{ $item->artwork->technique }}</p>
                                    </div>
                                    <button
                                        wire:click="removeItem({{ $item->id }})"
                                        class="text-gray-300 hover:text-red-500 transition p-1"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="mt-3 flex items-end justify-between">
                                    <a href="{{ route('artwork.detail', $item->artwork->slug) }}" class="text-[10px] text-gray-400 hover:text-brand-black100 transition link-underline pb-0.5">
                                        Eseri Görüntüle
                                    </a>
                                    <div class="text-right">
                                        <p class="font-medium text-brand-black100 text-sm">{{ $item->artwork->formatted_price_tl }}</p>
                                        <p class="text-gray-400 text-[10px]">{{ $item->artwork->formatted_price_usd }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 p-6 sticky top-24">
                        <h2 class="text-sm font-semibold text-brand-black100 mb-6 uppercase tracking-wider">Sipariş Özeti</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Ara Toplam</span>
                                <span class="font-medium text-brand-black100">{{ number_format($totalTl, 0, ',', '.') }} TL</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-400"></span>
                                <span class="text-gray-400">{{ number_format($totalUsd, 0, ',', '.') }} $</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Kargo</span>
                                <span class="text-green-600 text-xs font-medium">Ücretsiz</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4 mb-6">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-brand-black100">Toplam</span>
                                <div class="text-right">
                                    <p class="font-semibold text-brand-black100">{{ number_format($totalTl, 0, ',', '.') }} TL</p>
                                    <p class="text-gray-400 text-xs">{{ number_format($totalUsd, 0, ',', '.') }} $</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('checkout') }}" class="w-full bg-brand-black100 hover:bg-black text-white py-3.5 text-sm font-medium transition flex items-center justify-center">
                            Siparişi Tamamla
                        </a>

                        <a href="{{ route('artworks') }}" class="w-full border border-gray-200 mt-3 py-3 text-xs text-gray-500 hover:bg-white transition flex items-center justify-center">
                            Alışverişe Devam Et
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-20">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <h2 class="text-xl font-light text-brand-black100 mb-2">Sepetiniz Boş</h2>
                <p class="text-gray-400 text-sm mb-8">Sepetinizde henüz eser bulunmuyor.</p>
                <a href="{{ route('artworks') }}" class="inline-flex items-center bg-brand-black100 hover:bg-black text-white px-8 py-3 text-sm font-medium transition">
                    Eserleri Keşfet
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        @endif
    </div>
</div>
