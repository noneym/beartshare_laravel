<div>
    <!-- Page Header -->
    <section class="bg-brand-black100 py-16">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-light text-white">Sanatçı<span class="font-semibold">larımız</span></h1>
            <p class="text-white/50 text-sm mt-2">BeArtShare koleksiyonundaki sanatçıları keşfedin</p>
        </div>
    </section>

    <div class="container mx-auto px-4 py-10">
        <!-- Search & Filter Bar -->
        <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-6">
            <div class="relative w-full max-w-sm">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Sanatçı ara..."
                    class="w-full border border-gray-200 px-4 py-2.5 pl-10 text-sm focus:outline-none focus:border-brand-black100 transition"
                >
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <p class="text-gray-400 text-xs hidden md:block">{{ $artists->total() }} sanatçı</p>
        </div>

        <!-- Artists Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-8">
            @forelse($artists as $artist)
                <a href="{{ route('artist.detail', $artist->slug) }}" class="text-center group" wire:key="artist-{{ $artist->id }}">
                    <div class="w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden border border-gray-200 group-hover:border-brand-black100 transition-all mx-auto mb-3">
                        @if($artist->avatar_url)
                            <img src="{{ $artist->avatar_url }}" alt="{{ $artist->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-50 flex items-center justify-center text-3xl font-light text-gray-300">
                                {{ mb_substr($artist->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h3 class="font-medium text-brand-black100 text-sm group-hover:underline transition">{{ $artist->name }}</h3>
                    <p class="text-gray-400 text-xs mt-0.5">{{ $artist->life_span }}</p>
                    <p class="text-gray-300 text-[10px] mt-1">{{ $artist->artworks_count }} eser</p>
                </a>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-gray-400 text-sm">Sanatçı bulunamadı.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $artists->links() }}
        </div>
    </div>
</div>
