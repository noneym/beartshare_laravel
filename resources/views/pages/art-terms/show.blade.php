<x-layouts.app
    :title="$term->title . ($term->title_tr ? ' (' . $term->title_tr . ')' : '') . ' | Sanat Terimleri | BeArtShare'"
    :metaDescription="\Illuminate\Support\Str::limit($term->text, 155)"
    :metaKeywords="'sanat terimleri, ' . mb_strtolower($term->title) . ($term->title_tr ? ', ' . mb_strtolower($term->title_tr) : '')"
>
    <div class="container mx-auto px-4 py-10">
        <nav class="text-xs text-gray-400 mb-8">
            <a href="{{ route('home') }}" class="hover:text-brand-black100">Ana Sayfa</a>
            <span class="mx-2">/</span>
            <a href="{{ route('art-terms') }}" class="hover:text-brand-black100">Sanat Terimleri</a>
            <span class="mx-2">/</span>
            <span class="text-gray-600">{{ $term->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14">
            <article class="lg:col-span-7">
                <h1 class="text-3xl md:text-4xl font-semibold text-brand-black100 leading-tight">
                    {{ $term->title }}
                    @if($term->title_tr)
                        <span class="block text-xl md:text-2xl font-normal text-gray-400 mt-2">({{ $term->title_tr }})</span>
                    @endif
                </h1>
                <div class="border-t border-gray-100 mt-8 pt-8 text-[15px] text-gray-600 leading-relaxed whitespace-pre-line">{{ $term->text }}</div>
            </article>

            @if($term->image)
                <div class="lg:col-span-5">
                    <img src="{{ $term->imageUrl('detail') }}" alt="{{ $term->title }}" class="w-full bg-gray-50">
                </div>
            @endif
        </div>

        @if($related->isNotEmpty())
            <section class="mt-16 pt-10 border-t border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-brand-black100">Diğer terimler</h2>
                    <a href="{{ route('art-terms') }}" class="text-xs text-gray-400 hover:text-brand-black100 transition">Tüm terimler</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($related as $item)
                        <a href="{{ route('art-terms.show', $item->slug) }}" class="group block">
                            <div class="aspect-square bg-gray-50 overflow-hidden mb-2">
                                @if($item->image)
                                    <img src="{{ $item->imageUrl('thumb') }}" alt="{{ $item->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @endif
                            </div>
                            <p class="text-sm font-medium text-brand-black100 group-hover:text-primary transition">{{ $item->title }}</p>
                            @if($item->title_tr)<p class="text-xs text-gray-400">({{ $item->title_tr }})</p>@endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
