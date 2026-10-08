<x-layouts.app
    title="Sanat Terimleri Sözlüğü | BeArtShare"
    metaDescription="Sanat akımları, teknikler ve malzemeler: BeArtShare sanat terimleri sözlüğünde yüzlerce terimin Türkçe açıklaması."
    metaKeywords="sanat terimleri, sanat sözlüğü, sanat akımları, resim teknikleri, beartshare"
>
    <section class="bg-brand-black100 py-16">
        <div class="container mx-auto px-4">
            <nav class="text-xs text-white/40 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Ana Sayfa</a>
                <span class="mx-2">/</span>
                <span class="text-white/70">Sanat Terimleri</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-semibold text-white">Sanat Terimleri</h1>
            <p class="text-white/50 text-sm mt-2">Sanat akımları, teknikler ve malzemeler üzerine {{ $groups->flatten()->count() }} terim</p>
        </div>
    </section>

    {{-- Harf menüsü --}}
    <div class="sticky top-0 z-20 bg-white/95 backdrop-blur border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex gap-1 overflow-x-auto py-3 scrollbar-hide">
                @foreach($groups->keys() as $letter)
                    <a href="#harf-{{ $letter }}" class="flex-shrink-0 w-9 h-9 flex items-center justify-center text-sm font-medium text-brand-black100 hover:bg-brand-black100 hover:text-white transition">{{ $letter }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        @foreach($groups as $letter => $terms)
            <section id="harf-{{ $letter }}" class="scroll-mt-24 mb-14">
                <h2 class="text-3xl font-semibold text-brand-black100 border-b border-gray-200 pb-3 mb-8">{{ $letter }}</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
                    @foreach($terms as $term)
                        <a href="{{ route('art-terms.show', $term->slug) }}" class="group block">
                            <div class="aspect-[4/3] bg-gray-50 overflow-hidden mb-4">
                                @if($term->image)
                                    <img src="{{ $term->imageUrl('thumb') }}" alt="{{ $term->title }}" loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @endif
                            </div>
                            <h3 class="text-base font-semibold text-brand-black100 group-hover:text-primary transition">{{ $term->title }}</h3>
                            @if($term->title_tr)
                                <p class="text-sm text-gray-500 mt-0.5">({{ $term->title_tr }})</p>
                            @endif
                            @if($term->text)
                                <p class="text-xs text-gray-400 leading-relaxed mt-2 line-clamp-3">{{ $term->text }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</x-layouts.app>
