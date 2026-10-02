<div x-data="{
        toast: { show: false, message: '', type: 'success' },
        showShareMenu: false,
        showLoginModal: false,
        lightbox: { open: false, index: 0 },
        images: @js($artwork->image_urls ?? []),
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
        },
        openLightbox(index = 0) {
            this.lightbox.index = index;
            this.lightbox.open = true;
            document.body.style.overflow = 'hidden';
        },
        closeLightbox() {
            this.lightbox.open = false;
            document.body.style.overflow = '';
        },
        nextImage() {
            if (this.images.length > 0) {
                this.lightbox.index = (this.lightbox.index + 1) % this.images.length;
            }
        },
        prevImage() {
            if (this.images.length > 0) {
                this.lightbox.index = (this.lightbox.index - 1 + this.images.length) % this.images.length;
            }
        }
     }"
     @cart-added.window="showToast($event.detail.message, 'success')"
     @cart-error.window="showToast($event.detail.message, 'error')"
     @cart-info.window="showToast($event.detail.message, 'info')"
     @toast.window="showToast($event.detail.message, $event.detail.type || 'success')"
     @show-login-modal.window="showLoginModal = true"
     @keydown.escape.window="closeLightbox()"
     @keydown.arrow-right.window="if(lightbox.open) nextImage()"
     @keydown.arrow-left.window="if(lightbox.open) prevImage()"
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
        <i class="ph ph-check-circle flex-shrink-0 text-xl" x-show="toast.type === 'success'"></i>
        <!-- Error icon -->
        <i class="ph ph-x flex-shrink-0 text-xl" x-show="toast.type === 'error'"></i>
        <!-- Info icon -->
        <i class="ph ph-info flex-shrink-0 text-xl" x-show="toast.type === 'info'"></i>
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
                <i class="ph ph-x text-xl"></i>
            </button>
            <div class="w-12 h-12 bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i class="ph ph-heart text-brand-black100 text-2xl"></i>
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

    <!-- Image Lightbox Modal -->
    <div x-show="lightbox.open"
         x-cloak
         class="fixed inset-0 z-[150] flex items-center justify-center"
         @click.self="closeLightbox()"
    >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/95" @click="closeLightbox()"></div>

        <!-- Close Button -->
        <button @click="closeLightbox()"
                class="absolute top-4 right-4 z-10 text-white/70 hover:text-white transition p-2">
            <i class="ph ph-x text-3xl"></i>
        </button>

        <!-- Image Counter -->
        <div class="absolute top-4 left-4 z-10 text-white/70 text-sm font-medium" x-show="images.length > 1">
            <span x-text="lightbox.index + 1"></span> / <span x-text="images.length"></span>
        </div>

        <!-- Previous Button -->
        <button x-show="images.length > 1"
                @click.stop="prevImage()"
                class="absolute left-4 z-10 text-white/70 hover:text-white transition p-3 bg-black/30 hover:bg-black/50 rounded-full">
            <i class="ph ph-caret-left text-2xl"></i>
        </button>

        <!-- Next Button -->
        <button x-show="images.length > 1"
                @click.stop="nextImage()"
                class="absolute right-4 z-10 text-white/70 hover:text-white transition p-3 bg-black/30 hover:bg-black/50 rounded-full">
            <i class="ph ph-caret-right text-2xl"></i>
        </button>

        <!-- Image Container -->
        <div class="relative max-w-[90vw] max-h-[90vh] flex items-center justify-center" @click.stop>
            <img :src="images[lightbox.index]"
                 alt="{{ $artwork->title }}"
                 class="max-w-full max-h-[90vh] object-contain"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
            >
        </div>

        <!-- Thumbnail Strip -->
        <div x-show="images.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 z-10 flex gap-2 bg-black/50 p-2 rounded-lg max-w-[90vw] overflow-x-auto">
            <template x-for="(img, idx) in images" :key="idx">
                <button @click.stop="lightbox.index = idx"
                        class="w-12 h-12 flex-shrink-0 overflow-hidden border-2 transition rounded"
                        :class="lightbox.index === idx ? 'border-white' : 'border-transparent opacity-50 hover:opacity-80'">
                    <img :src="img" alt="" class="w-full h-full object-cover">
                </button>
            </template>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="border-b border-gray-100">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center space-x-2 text-sm text-gray-400">
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
                <div class="relative bg-gray-100 overflow-hidden aspect-square mb-3 cursor-zoom-in"
                     @if($artwork->image_urls && count($artwork->image_urls) > 0)
                     @click="openLightbox({{ $currentImage }})"
                     @endif
                >
                    @if($artwork->image_urls && count($artwork->image_urls) > 0)
                        <img src="{{ $artwork->image_urls[$currentImage] }}" alt="{{ $artwork->title }}" class="w-full h-full object-contain">
                        <div class="absolute bottom-3 right-3 bg-white/90 w-9 h-9 flex items-center justify-center text-gray-600 pointer-events-none">
                            <i class="ph ph-magnifying-glass-plus text-lg"></i>
                        </div>
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="ph ph-image text-6xl text-gray-300"></i>
                        </div>
                    @endif
                </div>

                @if($artwork->image_urls && count($artwork->image_urls) > 1)
                    <div class="flex gap-2">
                        @foreach($artwork->image_urls as $index => $imageUrl)
                            <button
                                wire:click="setImage({{ $index }})"
                                @dblclick="openLightbox({{ $index }})"
                                class="w-16 h-16 bg-gray-100 overflow-hidden border transition {{ $currentImage == $index ? 'border-brand-black100' : 'border-transparent opacity-70 hover:opacity-100' }}"
                                title="Tam boyut için çift tıklayın"
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
                    <a href="{{ route('artist.detail', $artwork->artist->slug) }}" class="text-base text-gray-500 hover:text-brand-black100 transition">
                        {{ $artwork->artist->name }} <span class="text-gray-400">{{ $artwork->artist->life_span }}</span>
                    </a>
                    <h1 class="text-3xl md:text-4xl font-semibold tracking-tight leading-tight text-brand-black100 mt-2">{{ $artwork->title }}</h1>
                </div>

                <!-- Artwork Details Table -->
                <div class="border-t border-gray-100 py-5 space-y-3 text-sm mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Teknik</span>
                        <span class="text-brand-black100">{{ $artwork->technique }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Boyut</span>
                        <span class="text-brand-black100">{{ $artwork->dimensions }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tarih</span>
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
                    <div class="flex items-baseline gap-3 mb-1">
                        <span class="text-2xl font-semibold text-brand-black100">{{ $artwork->formatted_price_tl }}</span>
                        <span class="text-gray-400 text-sm">{{ $artwork->formatted_price_usd }}</span>
                    </div>
                    <div class="mb-3">
                        <x-credit-card-badge :artwork="$artwork" class="text-xs px-2 py-1" />
                    </div>

                    @if(!$artwork->is_sold && $artpuanEarn > 0)
                        <div class="flex items-center gap-2 text-xs text-primary bg-primary/5 border border-primary/20 px-3 py-2 mb-4">
                            <i class="ph ph-star flex-shrink-0 text-base"></i>
                            <span>Bu eseri satın aldığınızda <strong>{{ number_format($artpuanEarn, 0, ',', '.') }} ArtPuan&reg;</strong> kazanırsınız</span>
                        </div>
                    @endif

                    @if($viewCount24h > 1 || $favoriteCount > 0)
                        <div class="flex items-center gap-4 text-xs text-gray-500 mb-5">
                            @if($viewCount24h > 1)
                                <span class="flex items-center gap-1.5">
                                    <i class="ph ph-eye text-gray-400 text-sm"></i>
                                    Son 24 saatte <strong class="text-brand-black100">{{ $viewCount24h }} kişi</strong> görüntüledi
                                </span>
                            @endif
                            @if($favoriteCount > 0)
                                <span class="flex items-center gap-1.5">
                                    <i class="ph ph-heart text-brand-black100 text-sm"></i>
                                    <strong class="text-brand-black100">{{ $favoriteCount }} koleksiyoner</strong> favorilerine ekledi
                                </span>
                            @endif
                        </div>
                    @endif

                    @if($artwork->is_sold)
                        <div class="bg-red-50 text-red-600 px-6 py-4 text-center text-sm font-medium">
                            Bu eser satılmıştır
                        </div>
                    @elseif($artwork->is_reserved)
                        <div class="bg-amber-50 text-amber-700 px-6 py-4 text-center text-sm font-medium flex items-center justify-center gap-2">
                            <i class="ph ph-clock text-base"></i>
                            Bu eser rezerve edilmiştir
                        </div>
                    @else
                        <button
                            wire:click="addToCart"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-wait"
                            class="w-full bg-brand-black100 hover:bg-black text-white py-3.5 text-sm font-medium transition flex items-center justify-center"
                        >
                            <i class="ph ph-shopping-bag mr-2 text-base" wire:loading.remove wire:target="addToCart"></i>
                            <i class="ph ph-spinner mr-2 animate-spin text-base" wire:loading wire:target="addToCart"></i>
                            <span wire:loading.remove wire:target="addToCart">Sepete Ekle</span>
                            <span wire:loading wire:target="addToCart">Ekleniyor...</span>
                        </button>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <!-- Favorilere Ekle / Kaldır -->
                            <button
                                wire:click="toggleFavorite"
                                wire:loading.attr="disabled"
                                class="relative py-3 text-sm transition flex items-center justify-center border btn-press
                                    {{ $isFavorited
                                        ? 'bg-brand-black100 border-brand-black100 text-white'
                                        : 'border-gray-300 text-brand-black100 hover:border-brand-black100' }}"
                            >
                                {{-- Loading spinner --}}
                                <i class="ph ph-spinner mr-1.5 animate-spin text-base" wire:loading wire:target="toggleFavorite"></i>

                                <span wire:loading.remove wire:target="toggleFavorite" class="flex items-center">
                                    @if($isFavorited)
                                        {{-- Filled heart --}}
                                        <i class="ph ph-heart mr-1.5 text-base"></i>
                                        Favorilerde
                                    @else
                                        {{-- Outline heart --}}
                                        <i class="ph ph-heart mr-1.5 text-base"></i>
                                        Favorilere Ekle
                                    @endif
                                </span>
                            </button>

                            <!-- Paylaş -->
                            <div class="relative">
                                <button
                                    @click="shareArtwork()"
                                    class="w-full border border-gray-300 py-3 text-sm text-brand-black100 hover:border-brand-black100 transition flex items-center justify-center btn-press"
                                >
                                    <i class="ph ph-share-network mr-1.5 text-base"></i>
                                    Paylaş
                                </button>

                                <!-- Share Dropdown (Desktop fallback) -->
                                <div x-show="showShareMenu"
                                     x-cloak
                                     @click.away="showShareMenu = false"
                                     x-transition
                                     class="absolute bottom-full left-0 right-0 mb-2 bg-white border border-gray-200 shadow-lg py-1 z-20"
                                >
                                    <a href="https://wa.me/?text={{ urlencode($artwork->title . ' - ' . url()->current()) }}" target="_blank" @click="showShareMenu = false" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50 transition">
                                        <i class="ph ph-whatsapp-logo text-gray-600 text-base"></i>
                                        WhatsApp
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($artwork->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" @click="showShareMenu = false" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50 transition">
                                        <i class="ph ph-x-logo text-gray-600 text-base"></i>
                                        X (Twitter)
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" @click="showShareMenu = false" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50 transition">
                                        <i class="ph ph-facebook-logo text-gray-600 text-base"></i>
                                        Facebook
                                    </a>
                                    <button @click="copyLink()" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50 transition">
                                        <i class="ph ph-link text-gray-500 text-base"></i>
                                        Linki Kopyala
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Artist Info -->
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex items-start gap-4">
                        <a href="{{ route('artist.detail', $artwork->artist->slug) }}" class="w-14 h-14 rounded-full overflow-hidden flex-shrink-0 bg-gray-100 ring-1 ring-gray-200">
                            @if($artwork->artist->avatar_url)
                                <img src="{{ $artwork->artist->avatar_url }}" alt="{{ $artwork->artist->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-lg text-gray-400">{{ mb_substr($artwork->artist->name, 0, 1) }}</div>
                            @endif
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('artist.detail', $artwork->artist->slug) }}" class="font-medium text-brand-black100 hover:text-primary transition">{{ $artwork->artist->name }}</a>
                            <p class="text-gray-400 text-sm">{{ $artwork->artist->life_span }}</p>
                            @if($artwork->artist->biography)
                                <p class="text-gray-600 text-sm mt-2 line-clamp-2 leading-relaxed">{{ $artwork->artist->biography }}</p>
                            @endif
                            <a href="{{ route('artist.detail', $artwork->artist->slug) }}" class="inline-flex items-center gap-1 text-sm text-brand-black100 underline underline-offset-4 hover:text-primary transition mt-2">
                                Sanatçının tüm eserleri <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Artworks -->
        @if($relatedArtworks->count() > 0)
            <div class="mt-16 pt-12 border-t border-gray-100">
                <h2 class="text-2xl font-semibold tracking-tight text-brand-black100 mb-8">Sanatçının diğer eserleri</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-8">
                    @foreach($relatedArtworks as $related)
                        <div wire:key="related-{{ $related->id }}">
                            <x-artwork-card :artwork="$related" aspect="aspect-[4/3]" size="sm" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
