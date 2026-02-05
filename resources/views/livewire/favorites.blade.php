<div x-data="{
        toast: { show: false, message: '', type: 'success' },
        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 3000);
        }
     }"
     @toast.window="showToast($event.detail.message, $event.detail.type || 'success')"
>
    {{-- Toast --}}
    <div x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] px-5 py-3 rounded-lg shadow-lg flex items-center gap-2 text-sm font-medium"
         :class="{
             'bg-green-600 text-white': toast.type === 'success',
             'bg-red-500 text-white': toast.type === 'error',
             'bg-blue-500 text-white': toast.type === 'info'
         }"
    >
        <svg x-show="toast.type === 'info'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span x-text="toast.message"></span>
    </div>

    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-b border-gray-100">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center space-x-2 text-xs text-gray-400">
                <a href="/" class="hover:text-brand-black100 transition">Ana Sayfa</a>
                <span>/</span>
                <span class="text-brand-black100">Favorilerim</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 lg:py-12">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-light text-brand-black100">
                Favori <span class="font-semibold">Eserlerim</span>
            </h1>
            @if($favorites->count() > 0)
                <span class="text-xs text-gray-400">{{ $favorites->count() }} eser</span>
            @endif
        </div>

        @if($favorites->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-8">
                @foreach($favorites as $fav)
                    @if($fav->artwork)
                    <div class="group relative" wire:key="fav-{{ $fav->id }}">
                        {{-- Remove button --}}
                        <button
                            wire:click="removeFavorite({{ $fav->artwork_id }})"
                            wire:loading.attr="disabled"
                            class="absolute top-2 right-2 z-10 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-white shadow-sm transition opacity-0 group-hover:opacity-100"
                            title="Favorilerden kaldır"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>

                        <a href="{{ route('artwork.detail', $fav->artwork->slug) }}" class="block">
                            <div class="relative bg-gray-50 overflow-hidden aspect-[4/3] mb-3">
                                @if($fav->artwork->is_sold)
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-[9px] px-2 py-0.5 z-10 uppercase tracking-wider">Satıldı</span>
                                @elseif($fav->artwork->is_reserved)
                                    <span class="absolute top-2 left-2 bg-amber-500 text-white text-[9px] px-2 py-0.5 z-10 uppercase tracking-wider">Rezerve</span>
                                @endif
                                @if($fav->artwork->first_image)
                                    <img src="{{ $fav->artwork->first_image_url }}" alt="{{ $fav->artwork->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <a href="{{ route('artist.detail', $fav->artwork->artist->slug ?? '') }}" class="text-gray-400 text-[10px] hover:text-brand-black100 transition">
                            {{ $fav->artwork->artist->name ?? '' }}
                        </a>
                        <h3 class="font-medium text-brand-black100 text-xs truncate mt-0.5">
                            <a href="{{ route('artwork.detail', $fav->artwork->slug) }}" class="hover:underline">
                                {{ $fav->artwork->title }}
                            </a>
                        </h3>
                        <p class="font-medium text-brand-black100 text-xs mt-1">{{ $fav->artwork->formatted_price_tl }}</p>
                    </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-light text-brand-black100 mb-2">Henüz favoriniz yok</h2>
                <p class="text-gray-400 text-sm mb-6">Beğendiğiniz eserleri favorilere ekleyerek kolayca takip edebilirsiniz.</p>
                <a href="{{ route('artworks') }}" class="inline-flex items-center gap-2 bg-brand-black100 hover:bg-black text-white px-6 py-2.5 text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Eserleri Keşfet
                </a>
            </div>
        @endif
    </div>
</div>
