<div>
    {{-- Sanatçı başlığı: açık zemin, avatar + biyografi --}}
    <section class="border-b border-gray-100">
        <div class="container mx-auto px-4 pt-10 pb-10 md:pt-14 md:pb-12">
            <nav class="text-sm text-gray-400 mb-6 flex items-center gap-2">
                <a href="/" class="hover:text-brand-black100 transition">Ana Sayfa</a>
                <span>/</span>
                <a href="{{ route('artists') }}" class="hover:text-brand-black100 transition">Sanatçılar</a>
                <span>/</span>
                <span class="text-gray-600">{{ $artist->name }}</span>
            </nav>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-start">
                <div class="md:col-span-3 lg:col-span-2">
                    <div class="w-32 h-32 md:w-full md:h-auto md:aspect-square rounded-full overflow-hidden bg-gray-100 ring-1 ring-gray-200">
                        @if($artist->avatar_url)
                            <img src="{{ $artist->avatar_url }}" alt="{{ $artist->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl text-gray-400">{{ mb_substr($artist->name, 0, 1) }}</div>
                        @endif
                    </div>
                </div>
                <div class="md:col-span-9 lg:col-span-8">
                    <h1 class="text-3xl md:text-4xl font-semibold tracking-tight text-brand-black100">{{ $artist->name }}</h1>
                    @if($artist->life_span)
                        <p class="text-gray-500 mt-1">{{ $artist->life_span }}</p>
                    @endif
                    @if($artist->biography)
                        <div x-data="{ expanded: false }" class="mt-5">
                            <p class="text-gray-600 leading-relaxed max-w-[65ch]" x-show="!expanded">{{ Str::limit($artist->biography, 320) }}</p>
                            <p class="text-gray-600 leading-relaxed max-w-[65ch]" x-show="expanded" x-cloak>{{ $artist->biography }}</p>
                            @if(strlen($artist->biography) > 320)
                                <button @click="expanded = !expanded" class="mt-3 text-sm font-medium text-brand-black100 underline underline-offset-4 hover:text-primary transition" x-text="expanded ? 'Daha az göster' : 'Devamını oku'"></button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Eserler --}}
    <div class="container mx-auto px-4 py-12">
        <h2 class="text-2xl font-semibold tracking-tight text-brand-black100 mb-8">
            Eserler <span class="text-gray-400 font-normal text-lg">({{ $artworks->total() }})</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
            @forelse($artworks as $artwork)
                <div wire:key="artwork-{{ $artwork->id }}">
                    <x-artwork-card :artwork="$artwork" />
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <i class="ph ph-image text-4xl text-gray-300"></i>
                    <p class="text-gray-500 mt-3">Bu sanatçıya ait eser bulunmuyor.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $artworks->links() }}
        </div>
    </div>
</div>
