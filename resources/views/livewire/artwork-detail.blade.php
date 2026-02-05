<div x-data="{
        toast: { show: false, message: '', type: 'success' },
        showShareMenu: false,
        showLoginModal: false,
        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 3000);
        },
        async shareArtwork() {
            const shareData = {
                title: '{{ addslashes($artwork->title) }} - {{ addslashes($artwork->artist->name) }}',
                text: '{{ addslashes($artwork->title) }} eserini BeArtShare\'de keşfet!',
                url: window.location.href
            };
            if (navigator.share) {
                try { await navigator.share(shareData); } catch(e) {}
            } else {
                this.showShareMenu = !this.showShareMenu;
            }
        },
        copyLink() {
            navigator.clipboard.writeText(window.location.href);
            this.showShareMenu = false;
            this.showToast('Link kopyalandı!', 'success');
        }
     }"
     @cart-added.window="showToast($event.detail.message, 'success')"
     @cart-error.window="showToast($event.detail.message, 'error')"
     @cart-info.window="showToast($event.detail.message, 'info')"
     @toast.window="showToast($event.detail.message, $event.detail.type || 'success')"
     @show-login-modal.window="showLoginModal = true"
>
    <!-- Toast Notification -->
    <div x-show="toast.show"
         x-cloak
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
        <!-- Success icon -->
        <svg x-show="toast.type === 'success'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <!-- Error icon -->
        <svg x-show="toast.type === 'error'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <!-- Info icon -->
        <svg x-show="toast.type === 'info'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span x-text="toast.message"></span>
    </div>

    <!-- Login Required Modal -->
    <div x-show="showLoginModal"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center"
    >
        <div class="absolute inset-0 bg-black/40" @click="showLoginModal = false"></div>
        <div x-show="showLoginModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-xl shadow-2xl p-6 w-[90%] max-w-sm text-center"
        >
            <button @click="showLoginModal = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-brand-black100 mb-2">Üye Girişi Gerekli</h3>
            <p class="text-sm text-gray-500 mb-6">Eserleri favorilerinize eklemek için giriş yapmanız veya üye olmanız gerekmektedir.</p>
            <div class="flex gap-3">
                <a href="{{ route('login') }}" class="flex-1 bg-brand-black100 hover:bg-black text-white py-2.5 text-sm font-medium rounded-lg transition text-center">
                    Giriş Yap
                </a>
                <button @click="showLoginModal = false" class="flex-1 border border-gray-200 py-2.5 text-sm text-gray-500 hover:bg-gray-50 rounded-lg transition">
                    Vazgeç
                </button>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="bg-gray-50 border-b border-gray-100">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center space-x-2 text-xs text-gray-400">
                <a href="/" class="hover:text-brand-black100 transition">Ana Sayfa</a>
                <span>/</span>
                <a href="{{ route('artworks') }}" class="hover:text-brand-black100 transition">Eserler</a>
                <span>/</span>
                <a href="{{ route('artist.detail', $artwork->artist->slug) }}" class="hover:text-brand-black100 transition">{{ $artwork->artist->name }}</a>
                <span>/</span>
                <span class="text-brand-black100">{{ $artwork->title }}</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16">
            <!-- Images -->
            <div>
                <div class="relative bg-gray-50 overflow-hidden aspect-square mb-4">
                    @if($artwork->image_urls && count($artwork->image_urls) > 0)
                        <img src="{{ $artwork->image_urls[$currentImage] }}" alt="{{ $artwork->title }}" class="w-full h-full object-contain">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-32 h-32 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                @if($artwork->image_urls && count($artwork->image_urls) > 1)
                    <div class="flex space-x-2">
                        @foreach($artwork->image_urls as $index => $imageUrl)
                            <button
                                wire:click="setImage({{ $index }})"
                                class="w-16 h-16 bg-gray-50 overflow-hidden border-2 transition {{ $currentImage == $index ? 'border-brand-black100' : 'border-gray-200 hover:border-gray-400' }}"
                            >
                                <img src="{{ $imageUrl }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Details -->
            <div>
                <div class="mb-6">
                    <a href="{{ route('artist.detail', $artwork->artist->slug) }}" class="text-sm text-gray-400 hover:text-brand-black100 transition link-underline pb-0.5">
                        {{ $artwork->artist->name }} {{ $artwork->artist->life_span }}
                    </a>
                    <h1 class="text-2xl md:text-3xl font-light text-brand-black100 mt-2">{{ $artwork->title }}</h1>
                </div>

                <!-- Artwork Details Table -->
                <div class="border-t border-gray-100 py-4 space-y-3 text-sm mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Teknik</span>
                        <span class="text-brand-black100">{{ $artwork->technique }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Boyut</span>
                        <span class="text-brand-black100">{{ $artwork->dimensions }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Yıl</span>
                        <span class="text-brand-black100">{{ $artwork->year }}</span>
                    </div>
                    @if($artwork->category)
                    <div class="flex justify-between">
                        <span class="text-gray-400">Kategori</span>
                        <span class="text-brand-black100">{{ $artwork->category->name }}</span>
                    </div>
                    @endif
                </div>

                @if($artwork->description)
                    <div class="border-t border-gray-100 py-4 mb-6">
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $artwork->description }}</p>
                    </div>
                @endif

                <!-- Price & Action -->
                <div class="border-t border-gray-100 pt-6 mb-6">
                    <div class="flex items-baseline gap-3 mb-6">
                        <span class="text-2xl font-semibold text-brand-black100">{{ $artwork->formatted_price_tl }}</span>
                        <span class="text-gray-400 text-sm">{{ $artwork->formatted_price_usd }}</span>
                    </div>

                    @if($artwork->is_sold)
                        <div class="bg-red-50 text-red-600 px-6 py-4 text-center text-sm font-medium">
                            Bu eser satılmıştır
                        </div>
                    @elseif($artwork->is_reserved)
                        <div class="bg-amber-50 text-amber-700 px-6 py-4 text-center text-sm font-medium flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Bu eser rezerve edilmiştir
                        </div>
                    @else
                        <button
                            wire:click="addToCart"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-wait"
                            class="w-full bg-brand-black100 hover:bg-black text-white py-3.5 text-sm font-medium transition flex items-center justify-center"
                        >
                            <svg wire:loading.remove wire:target="addToCart" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <svg wire:loading wire:target="addToCart" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="addToCart">Sepete Ekle</span>
                            <span wire:loading wire:target="addToCart">Ekleniyor...</span>
                        </button>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <!-- Favorilere Ekle / Kaldır -->
                            <button
                                wire:click="toggleFavorite"
                                wire:loading.attr="disabled"
                                class="relative py-3 text-xs transition flex items-center justify-center border
                                    {{ $isFavorited
                                        ? 'bg-red-50 border-red-200 text-red-500 hover:bg-red-100'
                                        : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}"
                            >
                                {{-- Loading spinner --}}
                                <svg wire:loading wire:target="toggleFavorite" class="w-4 h-4 mr-1.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>

                                <span wire:loading.remove wire:target="toggleFavorite" class="flex items-center">
                                    @if($isFavorited)
                                        {{-- Filled heart --}}
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                        Favorilerde
                                    @else
                                        {{-- Outline heart --}}
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                        Favorilere Ekle
                                    @endif
                                </span>
                            </button>

                            <!-- Paylaş -->
                            <div class="relative">
                                <button
                                    @click="shareArtwork()"
                                    class="w-full border border-gray-200 py-3 text-xs text-gray-500 hover:bg-gray-50 transition flex items-center justify-center"
                                >
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                    Paylaş
                                </button>

                                <!-- Share Dropdown (Desktop fallback) -->
                                <div x-show="showShareMenu"
                                     x-cloak
                                     @click.away="showShareMenu = false"
                                     x-transition
                                     class="absolute bottom-full left-0 right-0 mb-2 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-20"
                                >
                                    <a href="https://wa.me/?text={{ urlencode($artwork->title . ' - ' . url()->current()) }}" target="_blank" @click="showShareMenu = false" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                        WhatsApp
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($artwork->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" @click="showShareMenu = false" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        X (Twitter)
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" @click="showShareMenu = false" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        Facebook
                                    </a>
                                    <button @click="copyLink()" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                        Linki Kopyala
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Artist Info -->
                <div class="bg-gray-50 p-5">
                    <div class="flex items-start gap-4">
                        <a href="{{ route('artist.detail', $artwork->artist->slug) }}" class="w-14 h-14 rounded-full overflow-hidden flex-shrink-0 border border-gray-200">
                            @if($artwork->artist->avatar_url)
                                <img src="{{ $artwork->artist->avatar_url }}" alt="{{ $artwork->artist->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-lg font-light text-gray-400">
                                    {{ mb_substr($artwork->artist->name, 0, 1) }}
                                </div>
                            @endif
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('artist.detail', $artwork->artist->slug) }}" class="font-medium text-brand-black100 text-sm hover:underline">
                                {{ $artwork->artist->name }}
                            </a>
                            <p class="text-gray-400 text-xs">{{ $artwork->artist->life_span }}</p>
                            @if($artwork->artist->biography)
                                <p class="text-gray-500 text-xs mt-2 line-clamp-2 leading-relaxed">{{ $artwork->artist->biography }}</p>
                            @endif
                            <a href="{{ route('artist.detail', $artwork->artist->slug) }}" class="text-xs text-gray-400 hover:text-brand-black100 transition mt-2 inline-block link-underline pb-0.5">
                                Sanatçının Tüm Eserleri &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Artworks -->
        @if($relatedArtworks->count() > 0)
            <div class="mt-16 pt-12 border-t border-gray-100">
                <h2 class="text-xl font-light text-brand-black100 mb-8">Sanatçının Diğer <span class="font-semibold">Eserleri</span></h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
                    @foreach($relatedArtworks as $related)
                        <div class="group" wire:key="related-{{ $related->id }}">
                            <a href="{{ route('artwork.detail', $related->slug) }}" class="block">
                                <div class="relative bg-gray-50 overflow-hidden aspect-[4/3] mb-3">
                                    @if($related->is_sold)
                                        <span class="absolute top-2 left-2 bg-red-500 text-white text-[9px] px-2 py-0.5 z-10 uppercase tracking-wider">Satıldı</span>
                                    @elseif($related->is_reserved)
                                        <span class="absolute top-2 left-2 bg-amber-500 text-white text-[9px] px-2 py-0.5 z-10 uppercase tracking-wider">Rezerve</span>
                                    @endif
                                    @if($related->first_image)
                                        <img src="{{ $related->first_image_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            <h3 class="font-medium text-brand-black100 text-xs truncate">{{ $related->title }}</h3>
                            <p class="font-medium text-brand-black100 text-xs mt-1">{{ $related->formatted_price_tl }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
