<div>
    {{-- ========== HERO: asimetrik split, sol metin / sağ gerçek eser görseli ========== --}}
    <section class="border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center py-12 lg:py-20">
                <div class="lg:col-span-6 xl:col-span-5">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-semibold tracking-tight leading-[1.05] text-brand-black100">
                        Yeni Çağın Sanat Galerisi
                    </h1>
                    <p class="mt-6 text-base md:text-lg text-gray-600 leading-relaxed max-w-[48ch]">
                        Ülkemizin kıymetli sanatçılarına ve uluslararası Blue Chip sanatçılara güvenle ve kazançla ulaşmanın yolu.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="{{ route('artworks') }}" class="btn-press inline-flex items-center gap-2 bg-brand-black100 text-white px-7 py-3.5 text-sm font-medium hover:bg-primary transition-colors">
                            Eserleri Keşfet
                            <i class="ph ph-arrow-right text-base"></i>
                        </a>
                        <a href="{{ route('artists') }}" class="btn-press inline-flex items-center gap-2 border border-gray-300 text-brand-black100 px-7 py-3.5 text-sm font-medium hover:border-brand-black100 transition-colors">
                            Sanatçılar
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-6 xl:col-span-7">
                    @if($heroArtwork)
                        <a href="{{ route('artwork.detail', $heroArtwork->slug) }}" class="group block">
                            <div class="relative bg-gray-100 aspect-[4/3] lg:aspect-[5/4] overflow-hidden">
                                <img src="{{ $heroArtwork->first_image_url }}" alt="{{ $heroArtwork->title }}, {{ $heroArtwork->artist->name }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.02]" fetchpriority="high">
                            </div>
                            <p class="mt-3 text-sm text-gray-500">
                                <span class="text-brand-black100 font-medium">{{ $heroArtwork->artist->name }}</span>, {{ $heroArtwork->title }}
                            </p>
                        </a>
                    @else
                        {{-- TODO: hero için öne çıkan eser görseli (admin: Eser > Öne Çıkar + görsel), 1600x1200 --}}
                        <div class="bg-gray-100 aspect-[4/3] lg:aspect-[5/4]"></div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ========== SANATÇILAR: yatay kaydırmalı avatar şeridi ========== --}}
    <section class="py-14 lg:py-20">
        <div class="container mx-auto px-4">
            <div class="flex items-end justify-between mb-8">
                <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Sanatçılar</h2>
                <a href="{{ route('artists') }}" class="text-sm text-gray-500 hover:text-brand-black100 transition inline-flex items-center gap-1">
                    Tümünü gör <i class="ph ph-arrow-right"></i>
                </a>
            </div>
            <div class="relative" x-data="{ el: null }" x-init="el = $refs.artistScroll">
                <div x-ref="artistScroll" class="flex gap-6 overflow-x-auto pb-2 scrollbar-hide scroll-smooth">
                    @foreach($artists as $artist)
                        <a href="{{ route('artist.detail', $artist->slug) }}" class="flex-shrink-0 w-[112px] text-center group" wire:key="artist-{{ $artist->id }}">
                            <div class="w-24 h-24 mx-auto rounded-full overflow-hidden bg-gray-100 ring-1 ring-gray-200 group-hover:ring-primary transition">
                                @if($artist->avatar_url)
                                    <img src="{{ $artist->avatar_url }}" alt="{{ $artist->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xl text-gray-400">{{ mb_substr($artist->name, 0, 1) }}</div>
                                @endif
                            </div>
                            <p class="mt-3 text-sm font-medium text-brand-black100 leading-tight line-clamp-2">{{ $artist->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $artist->life_span }}</p>
                        </a>
                    @endforeach
                </div>
                <button @click="el.scrollBy({left: -480, behavior: 'smooth'})" aria-label="Geri" class="hidden lg:flex absolute -left-5 top-10 w-10 h-10 bg-white border border-gray-200 items-center justify-center hover:border-brand-black100 transition">
                    <i class="ph ph-caret-left"></i>
                </button>
                <button @click="el.scrollBy({left: 480, behavior: 'smooth'})" aria-label="İleri" class="hidden lg:flex absolute -right-5 top-10 w-10 h-10 bg-white border border-gray-200 items-center justify-center hover:border-brand-black100 transition">
                    <i class="ph ph-caret-right"></i>
                </button>
            </div>
        </div>
    </section>

    {{-- ========== ÖNE ÇIKAN ESERLER: bento (1 büyük + 2 küçük + kalanlar) ========== --}}
    @if($featuredArtworks->count() > 0)
    @php
        $featured = $featuredArtworks->values();
        $lead = $featured->get(0);
        $side = $featured->slice(1, 2);
        $rest = $featured->slice(3, 3);
    @endphp
    <section class="py-14 lg:py-20 bg-gray-50 border-y border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex items-end justify-between mb-8">
                <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Öne Çıkan Eserler</h2>
                <a href="{{ route('artworks') }}" class="text-sm text-gray-500 hover:text-brand-black100 transition inline-flex items-center gap-1">
                    Tümünü gör <i class="ph ph-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Büyük kart --}}
                <div class="lg:col-span-2 reveal" x-data x-intersect.once="$el.classList.add('is-in')" wire:key="featured-{{ $lead->id }}">
                    <x-artwork-card :artwork="$lead" aspect="aspect-[4/3]" size="lg" />
                </div>
                {{-- Yan iki kart --}}
                <div class="grid grid-cols-2 lg:grid-cols-1 gap-8 lg:gap-6">
                    @foreach($side as $i => $artwork)
                        <div class="reveal" x-data x-intersect.once="$el.classList.add('is-in')" wire:key="featured-{{ $artwork->id }}">
                            <x-artwork-card :artwork="$artwork" aspect="aspect-[4/3]" />
                        </div>
                    @endforeach
                </div>
            </div>

            @if($rest->count() > 0)
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-8 mt-8">
                @foreach($rest as $artwork)
                    <div class="reveal" x-data x-intersect.once="$el.classList.add('is-in')" wire:key="featured-{{ $artwork->id }}">
                        <x-artwork-card :artwork="$artwork" aspect="aspect-[4/3]" />
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ========== SON EKLENEN: 4 sütun düz ızgara ========== --}}
    <section class="py-14 lg:py-20">
        <div class="container mx-auto px-4">
            <div class="flex items-end justify-between mb-8">
                <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Son Eklenen Eserler</h2>
                <a href="{{ route('artworks') }}" class="text-sm text-gray-500 hover:text-brand-black100 transition inline-flex items-center gap-1">
                    Tümünü gör <i class="ph ph-arrow-right"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
                @foreach($latestArtworks as $artwork)
                    <div wire:key="latest-{{ $artwork->id }}">
                        <x-artwork-card :artwork="$artwork" aspect="aspect-square" size="sm" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========== ESER KABULÜ + ARTPUAN: iki panelli teklif bandı ========== --}}
    <section class="py-14 lg:py-20 border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <a href="{{ route('eser-kabulu') }}" class="lg:col-span-3 group bg-gray-100 p-8 lg:p-12 flex flex-col justify-between min-h-[280px] hover:bg-gray-200/70 transition-colors">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Koleksiyonerden koleksiyonere</h2>
                        <p class="mt-3 text-gray-600 leading-relaxed max-w-[48ch]">Elinizdeki eserleri düşük komisyonla, şeffaf ve güvenli bir şekilde yeni koleksiyonerlere ulaştırın.</p>
                    </div>
                    <span class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-brand-black100">
                        Eser Kabulü <i class="ph ph-arrow-right transition-transform group-hover:translate-x-1"></i>
                    </span>
                </a>
                <a href="{{ route('artpuan') }}" class="lg:col-span-2 group bg-primary/10 p-8 lg:p-12 flex flex-col justify-between min-h-[280px] hover:bg-primary/15 transition-colors">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">ArtPuan&reg;</h2>
                        <p class="mt-3 text-gray-700 leading-relaxed">Her alışverişte tutarın %1'i puan olarak hesabınıza döner. Referans olduğunuz kişilerin alımlarından da kazanmaya devam edersiniz.</p>
                    </div>
                    <span class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-brand-black100">
                        Nasıl çalışır <i class="ph ph-arrow-right transition-transform group-hover:translate-x-1"></i>
                    </span>
                </a>
            </div>
        </div>
    </section>

    {{-- ========== SATILAN ESERLER: 6'lı küçük şerit ========== --}}
    @if($soldArtworks->count() > 0)
    <section class="py-14 lg:py-20 bg-gray-50 border-y border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Satılan Eserler</h2>
                    <p class="text-gray-500 mt-1">Koleksiyonerlerimize ulaşan eserler</p>
                </div>
                <a href="{{ route('artworks', ['satilanlar' => 'only']) }}" class="text-sm text-gray-500 hover:text-brand-black100 transition inline-flex items-center gap-1">
                    Tümünü gör <i class="ph ph-arrow-right"></i>
                </a>
            </div>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
                @foreach($soldArtworks as $artwork)
                    <a href="{{ route('artwork.detail', $artwork->slug) }}" class="group" wire:key="sold-{{ $artwork->id }}">
                        <div class="aspect-square overflow-hidden bg-white">
                            @if($artwork->first_image)
                                <img src="{{ $artwork->first_image_url }}" alt="{{ $artwork->title }}" class="w-full h-full object-cover grayscale-[35%] opacity-80 group-hover:opacity-100 group-hover:grayscale-0 transition duration-500">
                            @endif
                        </div>
                        <p class="mt-2 text-xs text-gray-500 truncate">{{ $artwork->artist->name }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ========== HABERLER: 1 öne çıkan + liste ========== --}}
    @if($blogPosts->count() > 0)
    @php $leadPost = $blogPosts->first(); $otherPosts = $blogPosts->slice(1, 3); @endphp
    <section class="py-14 lg:py-20">
        <div class="container mx-auto px-4">
            <div class="flex items-end justify-between mb-8">
                <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Haberler</h2>
                <a href="{{ route('blog') }}" class="text-sm text-gray-500 hover:text-brand-black100 transition inline-flex items-center gap-1">
                    Tümünü gör <i class="ph ph-arrow-right"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <a href="{{ route('blog.detail', $leadPost->slug) }}" class="lg:col-span-7 group" wire:key="blog-{{ $leadPost->id }}">
                    <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                        <img src="{{ $leadPost->image_url }}" alt="{{ $leadPost->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.02]">
                    </div>
                    <h3 class="mt-5 text-xl md:text-2xl font-semibold tracking-tight text-brand-black100 group-hover:text-primary transition leading-snug">{{ $leadPost->title }}</h3>
                    <p class="mt-2 text-gray-600 line-clamp-2 max-w-[60ch]">{{ $leadPost->excerpt }}</p>
                    <p class="mt-3 text-sm text-gray-400 italic">{{ $leadPost->created_at->format('d.m.Y') }}</p>
                </a>
                <div class="lg:col-span-5 divide-y divide-gray-100">
                    @foreach($otherPosts as $post)
                        <a href="{{ route('blog.detail', $post->slug) }}" class="group flex gap-5 py-5 first:pt-0" wire:key="blog-{{ $post->id }}">
                            <div class="w-28 h-20 flex-shrink-0 overflow-hidden bg-gray-100">
                                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-medium text-brand-black100 group-hover:text-primary transition line-clamp-2 leading-snug">{{ $post->title }}</h3>
                                <p class="mt-1.5 text-sm text-gray-400 italic">{{ $post->created_at->format('d.m.Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ========== NEDEN BEARTSHARE: dikey ifade listesi ========== --}}
    <section class="py-14 lg:py-20 border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <div class="lg:col-span-4">
                    <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Neden BeArtShare?</h2>
                </div>
                <ul class="lg:col-span-8 divide-y divide-gray-100">
                    <li class="flex gap-5 py-6 first:pt-0">
                        <i class="ph ph-seal-check text-2xl text-primary flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="text-lg font-medium text-brand-black100">Orijinallik garantisi</h3>
                            <p class="mt-1 text-gray-600 leading-relaxed max-w-[60ch]">Her eser uzman ekibimiz tarafından incelenir ve orijinallik garantisiyle teslim edilir.</p>
                        </div>
                    </li>
                    <li class="flex gap-5 py-6">
                        <i class="ph ph-package text-2xl text-primary flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="text-lg font-medium text-brand-black100">Sigortalı kargo, özel paketleme</h3>
                            <p class="mt-1 text-gray-600 leading-relaxed max-w-[60ch]">Eserler profesyonel sanat paketleme yöntemleriyle hazırlanır ve sigortalı olarak kapınıza gelir.</p>
                        </div>
                    </li>
                    <li class="flex gap-5 py-6 last:pb-0">
                        <i class="ph ph-shield-check text-2xl text-primary flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="text-lg font-medium text-brand-black100">Güvenli ödeme</h3>
                            <p class="mt-1 text-gray-600 leading-relaxed max-w-[60ch]">Banka altyapısı üzerinden 3D Secure ile kredi kartı, ya da havale/EFT. Kart bilgileriniz bizde tutulmaz.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>
