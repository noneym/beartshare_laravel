<div>
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
                    @else
                        <button
                            wire:click="addToCart"
                            class="w-full bg-brand-black100 hover:bg-black text-white py-3.5 text-sm font-medium transition flex items-center justify-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Sepete Ekle
                        </button>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <button class="border border-gray-200 py-3 text-xs text-gray-500 hover:bg-gray-50 transition flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                Favorilere Ekle
                            </button>
                            <button class="border border-gray-200 py-3 text-xs text-gray-500 hover:bg-gray-50 transition flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                </svg>
                                Paylaş
                            </button>
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
