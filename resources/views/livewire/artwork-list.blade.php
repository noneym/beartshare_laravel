<div>
    <!-- Page Header -->
    <section class="bg-brand-black100 py-16">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-light text-white">Eser<span class="font-semibold">ler</span></h1>
            <p class="text-white/50 text-sm mt-2">BeArtShare koleksiyonundaki tüm eserleri keşfedin</p>
        </div>
    </section>

    <div class="container mx-auto px-4 py-10">
        <!-- Filters -->
        <div class="border-b border-gray-100 pb-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div class="lg:col-span-2 relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Eser veya sanatçı ara..."
                        class="w-full border border-gray-200 px-4 py-2.5 pl-10 text-sm focus:outline-none focus:border-brand-black100 transition"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Artist Filter -->
                <select
                    wire:model.live="artistId"
                    class="w-full border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-brand-black100 bg-white transition"
                >
                    <option value="">Tüm Sanatçılar</option>
                    @foreach($artists as $artist)
                        <option value="{{ $artist->id }}">{{ $artist->name }}</option>
                    @endforeach
                </select>

                <!-- Price Filter -->
                <select
                    wire:model.live="priceRange"
                    class="w-full border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-brand-black100 bg-white transition"
                >
                    <option value="">Tüm Fiyatlar</option>
                    <option value="under_100k">100.000 TL altı</option>
                    <option value="100k_500k">100.000 - 500.000 TL</option>
                    <option value="500k_1m">500.000 - 1.000.000 TL</option>
                    <option value="over_1m">1.000.000 TL üzeri</option>
                </select>

                <!-- Sort -->
                <select
                    wire:model.live="sortBy"
                    class="w-full border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-brand-black100 bg-white transition"
                >
                    <option value="latest">En Yeni</option>
                    <option value="oldest">En Eski</option>
                    <option value="price_asc">Fiyat (Artan)</option>
                    <option value="price_desc">Fiyat (Azalan)</option>
                    <option value="name">İsim (A-Z)</option>
                </select>
            </div>
            <p class="text-gray-400 text-xs mt-3">{{ $artworks->total() }} eser listeleniyor</p>
        </div>

        <!-- Artworks Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
            @forelse($artworks as $artwork)
                <div class="group" wire:key="artwork-{{ $artwork->id }}">
                    <a href="{{ route('artwork.detail', $artwork->slug) }}" class="block">
                        <div class="relative bg-gray-50 overflow-hidden aspect-[4/3] mb-4">
                            @if($artwork->is_sold)
                                <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] px-3 py-1 z-10 uppercase tracking-wider">Satıldı</span>
                            @elseif($artwork->is_reserved)
                                <span class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] px-3 py-1 z-10 uppercase tracking-wider">Rezerve</span>
                            @endif
                            @if($artwork->first_image)
                                <img src="{{ $artwork->first_image_url }}" alt="{{ $artwork->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-300"></div>
                        </div>
                    </a>

                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0 pr-4">
                            <h3 class="font-medium text-brand-black100 text-sm truncate">{{ $artwork->artist->name }}</h3>
                            <p class="text-gray-500 text-xs mt-0.5">{{ $artwork->artist->life_span }}</p>
                            <p class="text-gray-400 text-xs mt-1 truncate">{{ $artwork->title }}</p>
                            <p class="text-gray-300 text-[10px] mt-0.5">{{ $artwork->technique }}, {{ $artwork->year }}</p>
                            <p class="text-gray-300 text-[10px]">{{ $artwork->dimensions }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-medium text-brand-black100 text-sm">{{ $artwork->formatted_price_tl }}</p>
                            <p class="text-gray-400 text-[10px]">{{ $artwork->formatted_price_usd }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-400 text-sm">Aradığınız kriterlere uygun eser bulunamadı.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $artworks->links() }}
        </div>
    </div>
</div>
