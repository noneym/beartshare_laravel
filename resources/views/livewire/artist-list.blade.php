<div>
    <x-page-header title="Sanatçılar" subtitle="BeArtShare koleksiyonundaki sanatçıları keşfedin" />

    <div class="container mx-auto px-4 py-10">
        <div class="flex items-center justify-between gap-4 mb-10">
            <label class="relative w-full max-w-sm">
                <span class="sr-only">Sanatçı ara</span>
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Sanatçı ara"
                       class="w-full border border-gray-200 pl-10 pr-4 py-2.5 text-sm text-brand-black100 placeholder:text-gray-400 focus:outline-none focus:border-brand-black100 transition">
            </label>
            <p class="text-sm text-gray-500 hidden md:block">{{ $artists->total() }} sanatçı</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-6 gap-y-10">
            @forelse($artists as $artist)
                <a href="{{ route('artist.detail', $artist->slug) }}" class="text-center group" wire:key="artist-{{ $artist->id }}">
                    <div class="w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden bg-gray-100 ring-1 ring-gray-200 group-hover:ring-primary transition mx-auto">
                        @if($artist->avatar_url)
                            <img src="{{ $artist->avatar_url }}" alt="{{ $artist->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-3xl text-gray-400">{{ mb_substr($artist->name, 0, 1) }}</div>
                        @endif
                    </div>
                    <h3 class="mt-4 font-medium text-brand-black100 group-hover:text-primary transition">{{ $artist->name }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $artist->life_span }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $artist->artworks_count }} eser</p>
                </a>
            @empty
                <div class="col-span-full py-16 text-center">
                    <i class="ph ph-user text-4xl text-gray-300"></i>
                    <p class="text-gray-500 mt-3">Aramanızla eşleşen sanatçı bulunamadı.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $artists->links() }}
        </div>
    </div>
</div>
