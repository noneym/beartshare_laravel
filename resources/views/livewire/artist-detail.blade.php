<div>
    <!-- Artist Header -->
    <section class="bg-brand-black100 py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden border-2 border-white/20 flex-shrink-0">
                    @if($artist->avatar_url)
                        <img src="{{ $artist->avatar_url }}" alt="{{ $artist->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-white/10 flex items-center justify-center text-5xl font-light text-white/40">
                            {{ mb_substr($artist->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="text-center md:text-left">
                    <nav class="text-white/40 text-xs mb-3">
                        <a href="/" class="hover:text-white/60 transition">Ana Sayfa</a>
                        <span class="mx-2">/</span>
                        <a href="{{ route('artists') }}" class="hover:text-white/60 transition">Sanatçılar</a>
                        <span class="mx-2">/</span>
                        <span class="text-white/70">{{ $artist->name }}</span>
                    </nav>
                    <h1 class="text-3xl md:text-4xl font-light text-white">{{ $artist->name }}</h1>
                    <p class="text-white/40 text-sm mt-1">{{ $artist->life_span }}</p>
                    @if($artist->biography)
                        <div x-data="{ expanded: false }">
                            <p class="text-white/60 text-sm mt-4 leading-relaxed max-w-2xl" x-show="!expanded">{{ Str::limit($artist->biography, 250) }}</p>
                            <p class="text-white/60 text-sm mt-4 leading-relaxed max-w-2xl" x-show="expanded" x-cloak>{{ $artist->biography }}</p>
                            @if(strlen($artist->biography) > 250)
                                <button @click="expanded = !expanded" class="text-white/40 text-xs mt-2 hover:text-white/70 transition underline" x-text="expanded ? 'Daha Az Göster' : 'Daha Fazla Göster'"></button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Artworks -->
    <div class="container mx-auto px-4 py-10">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-light text-brand-black100">
                Eserler <span class="text-gray-300 text-sm">({{ $artworks->total() }})</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
            @forelse($artworks as $artwork)
                <div class="group" wire:key="artwork-{{ $artwork->id }}">
                    <a href="{{ route('artwork.detail', $artwork->slug) }}" class="block">
                        <div class="relative bg-gray-50 overflow-hidden aspect-[4/3] mb-4">
                            @if($artwork->is_sold)
                                <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] px-3 py-1 z-10 uppercase tracking-wider">Satıldı</span>
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
                            <h3 class="font-medium text-brand-black100 text-sm truncate">{{ $artwork->title }}</h3>
                            <p class="text-gray-400 text-xs mt-1">{{ $artwork->technique }}, {{ $artwork->year }}</p>
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
                    <p class="text-gray-400 text-sm">Bu sanatçıya ait eser bulunamadı.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $artworks->links() }}
        </div>
    </div>
</div>
