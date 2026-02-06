<div>
    <!-- Hero Slider -->
    <section class="relative overflow-hidden" x-data="heroSlider()" x-init="startAutoPlay()">
        <style>
            .hero-slider-wrapper {
                position: relative;
                width: 100%;
                height: 340px;
                overflow: hidden;
            }
            @media(min-width: 768px) { .hero-slider-wrapper { height: 400px; } }
            @media(min-width: 1024px) { .hero-slider-wrapper { height: 440px; } }

            .hero-slide {
                position: absolute;
                top: 0; left: 0;
                width: 100%; height: 100%;
                opacity: 0;
                transition: opacity 1s cubic-bezier(0.4, 0, 0.2, 1);
                pointer-events: none;
                z-index: 1;
            }
            .hero-slide.active {
                opacity: 1;
                pointer-events: auto;
                z-index: 2;
            }

            /* Large background typography */
            .bg-typo {
                font-family: 'Prompt', sans-serif;
                font-weight: 900;
                line-height: 0.82;
                letter-spacing: -0.03em;
                user-select: none;
                pointer-events: none;
            }
            .bg-typo-xl { font-size: clamp(14rem, 28vw, 26rem); }
            .bg-typo-lg { font-size: clamp(10rem, 20vw, 18rem); }
            .bg-typo-md { font-size: clamp(7rem, 14vw, 12rem); }

            /* Slide content visibility */
            .slide-title,
            .slide-desc,
            .slide-btn {
                opacity: 0;
                transform: translateY(18px);
                transition: opacity 0.6s ease, transform 0.6s ease;
            }
            .hero-slide.active .slide-title {
                opacity: 1 !important;
                transform: translateY(0) !important;
                transition-delay: 0.1s;
            }
            .hero-slide.active .slide-desc {
                opacity: 1 !important;
                transform: translateY(0) !important;
                transition-delay: 0.25s;
            }
            .hero-slide.active .slide-btn {
                opacity: 1 !important;
                transform: translateY(0) !important;
                transition-delay: 0.4s;
            }

            /* Progress bar for active dot */
            .dot-progress {
                position: relative;
                overflow: hidden;
            }
            .dot-progress.active::after {
                content: '';
                position: absolute;
                bottom: 0; left: 0;
                height: 100%; width: 0;
                background: rgba(255,255,255,0.5);
                border-radius: 999px;
                animation: dotFill 5s linear forwards;
            }
            @keyframes dotFill {
                from { width: 0; }
                to { width: 100%; }
            }
        </style>

        <div class="hero-slider-wrapper">

            <!-- ==================== SLIDE 1: BeArtShare - Dark ==================== -->
            <div class="hero-slide" :class="{ 'active': currentSlide === 0 }">
                <div class="w-full h-full relative" style="background: linear-gradient(160deg, #14171c 0%, #1a1e25 40%, #1f242d 70%, #14171c 100%);">
                    <!-- Background Typography Layer -->
                    <div class="absolute inset-0 overflow-hidden">
                        <span class="bg-typo bg-typo-xl absolute -left-[3%] -top-[5%]" style="color: rgba(255,255,255,0.03);">ART</span>
                        <span class="bg-typo bg-typo-lg absolute right-[-6%] top-[0%]" style="color: rgba(255,255,255,0.025);">SHARE</span>
                        <span class="bg-typo bg-typo-xl absolute left-[10%] bottom-[-25%]" style="color: rgba(255,255,255,0.02);">BE</span>
                    </div>

                    <!-- Subtle decorative accent line -->
                    <div class="absolute top-0 left-0 w-full h-[2px]" style="background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.08) 30%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.08) 70%, transparent 100%);"></div>

                    <!-- Content Layer - Centered -->
                    <div class="relative z-10 h-full">
                        <div class="container mx-auto px-6 lg:px-8 h-full flex items-center justify-center">
                            <div class="text-center max-w-2xl">
                                <p class="slide-desc text-white/40 text-[10px] md:text-xs tracking-[0.35em] uppercase mb-5">Yeni Çağın Sanat Galerisi</p>
                                <h2 class="slide-title text-5xl md:text-6xl lg:text-[5.5rem] font-bold text-white leading-[0.95] tracking-tight">
                                    BeArt<span class="font-light">Share</span>
                                </h2>
                                <p class="slide-desc text-white/55 text-sm md:text-[15px] leading-[1.9] mt-6 max-w-lg mx-auto">
                                    Ülkemizin kıymetli sanatçılarına ve uluslararası Blue Chip sanatçılara güvenle ve kazançla ulaşmanın yolu.
                                </p>
                                <div class="slide-btn mt-8 flex items-center justify-center gap-4">
                                    <a href="{{ route('artworks') }}" class="inline-flex items-center bg-white text-brand-black100 pl-7 pr-5 py-3 rounded-full text-sm font-semibold hover:shadow-xl hover:scale-[1.02] transition-all duration-300 shadow-lg group">
                                        Hemen Başla
                                        <svg class="w-4 h-4 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    <a href="{{ route('artists') }}" class="inline-flex items-center border border-white/20 text-white pl-7 pr-5 py-3 rounded-full text-sm font-medium hover:bg-white/10 transition-all duration-300 group">
                                        Sanatçılar
                                        <svg class="w-4 h-4 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Bar: Dots + Hashtag -->
                        <div class="absolute bottom-5 left-0 right-0">
                            <div class="container mx-auto px-6 lg:px-8 flex items-center justify-center">
                                <div class="flex items-center gap-2.5">
                                    <template x-for="(s, i) in 3" :key="i">
                                        <button @click="goToSlide(i)"
                                            class="dot-progress rounded-full transition-all duration-300"
                                            :class="currentSlide === i ? 'active w-8 h-2.5 bg-white' : 'w-2.5 h-2.5 bg-white/35 hover:bg-white/55'"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== SLIDE 2: Eser Kabulü - Purple ==================== -->
            <div class="hero-slide" :class="{ 'active': currentSlide === 1 }">
                <div class="w-full h-full relative" style="background: linear-gradient(135deg, #5C4290 0%, #7052A8 25%, #8664BE 60%, #7052A8 100%);">
                    <!-- Background Typography Layer -->
                    <div class="absolute inset-0 overflow-hidden">
                        <span class="bg-typo bg-typo-xl absolute -left-[3%] -top-[5%]" style="color: rgba(80,55,140,0.15);">BE</span>
                        <span class="bg-typo bg-typo-lg absolute right-[-6%] top-[0%]" style="color: rgba(80,55,140,0.12);">ART</span>
                        <span class="bg-typo bg-typo-md absolute left-[30%] bottom-[-15%]" style="color: rgba(60,35,120,0.10);">SHARE</span>
                    </div>

                    <!-- Content Layer -->
                    <div class="relative z-10 h-full">
                        <div class="container mx-auto px-6 lg:px-8 h-full flex items-center">
                            <div class="w-full grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-center">
                                <!-- Left: Title & Button -->
                                <div class="lg:col-span-5">
                                    <div class="slide-title">
                                        <span class="inline-block text-white/40 text-xs font-medium tracking-[0.25em] uppercase mb-4">Sanatçılar İçin</span>
                                        <h2 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white leading-[0.95] tracking-tight" style="text-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                                            Eser<br>Kabulü
                                        </h2>
                                    </div>
                                    <div class="slide-btn mt-8">
                                        <a href="{{ route('eser-kabulu') }}" class="inline-flex items-center bg-white text-gray-800 pl-7 pr-5 py-3 rounded-full text-sm font-semibold hover:shadow-xl hover:scale-[1.02] transition-all duration-300 shadow-lg group">
                                            Eser Kabulü
                                            <svg class="w-4 h-4 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                                <!-- Right: Description -->
                                <div class="lg:col-span-6 lg:col-start-7">
                                    <p class="slide-desc text-white/85 text-sm md:text-[15px] leading-[1.8] max-w-md">
                                        Elinizdeki eserleri BeArtShare aracılığıyla, çok düşük komisyon oranlarıyla şeffaf ve güvenli bir şekilde satışa çıkarın. Profesyonel fotoğraflama ve kataloglama hizmeti ile eserlerinizi en iyi şekilde tanıtıyoruz.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Bar: Dots + Hashtag -->
                        <div class="absolute bottom-5 left-0 right-0">
                            <div class="container mx-auto px-6 lg:px-8 flex items-center justify-between">
                                <div></div>
                                <div class="flex items-center gap-6">
                                    <div class="flex items-center gap-2.5">
                                        <template x-for="(s, i) in 3" :key="i">
                                            <button @click="goToSlide(i)"
                                                class="dot-progress rounded-full transition-all duration-300"
                                                :class="currentSlide === i ? 'active w-8 h-2.5 bg-white' : 'w-2.5 h-2.5 bg-white/35 hover:bg-white/55'"
                                            ></button>
                                        </template>
                                    </div>
                                    <span class="text-white/50 text-[11px] font-medium tracking-wide hidden md:block">#YeniÇağınSanatGalerisi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== SLIDE 3: ArtPuan - Red ==================== -->
            <div class="hero-slide" :class="{ 'active': currentSlide === 2 }">
                <div class="w-full h-full relative" style="background: linear-gradient(135deg, #B31B1B 0%, #D42828 25%, #E83535 60%, #D42828 100%);">
                    <!-- Background Typography Layer -->
                    <div class="absolute inset-0 overflow-hidden">
                        <span class="bg-typo bg-typo-xl absolute -left-[3%] -top-[5%]" style="color: rgba(150,18,18,0.17);">ART</span>
                        <span class="bg-typo bg-typo-lg absolute right-[-6%] top-[0%]" style="color: rgba(150,18,18,0.13);">SHARE</span>
                        <span class="bg-typo bg-typo-xl absolute left-[10%] bottom-[-25%]" style="color: rgba(130,12,12,0.10);">ART</span>
                    </div>

                    <!-- Content Layer -->
                    <div class="relative z-10 h-full">
                        <div class="container mx-auto px-6 lg:px-8 h-full flex items-center">
                            <div class="w-full grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-center">
                                <!-- Left: Title & Button -->
                                <div class="lg:col-span-5">
                                    <div class="slide-title">
                                        <span class="inline-block text-white/40 text-xs font-medium tracking-[0.25em] uppercase mb-4">Sadakat Programı</span>
                                        <h2 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white leading-[0.95] tracking-tight" style="text-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                                            Art<br>Puan
                                        </h2>
                                    </div>
                                    <div class="slide-btn mt-8">
                                        <a href="{{ route('artpuan') }}" class="inline-flex items-center bg-white text-gray-800 pl-7 pr-5 py-3 rounded-full text-sm font-semibold hover:shadow-xl hover:scale-[1.02] transition-all duration-300 shadow-lg group">
                                            ArtPuan
                                            <svg class="w-4 h-4 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                                <!-- Right: Description + Feature Tags -->
                                <div class="lg:col-span-6 lg:col-start-7">
                                    <p class="slide-desc text-white/85 text-sm md:text-[15px] leading-[1.8] max-w-md">
                                        Satın aldığınız her eserden tutarın %1'i oranında ArtPuan kazanın. Üstelik çevrenize referans olarak onların da satın alımlarından %1 kazanmaya devam edin.
                                    </p>
                                    <div class="slide-btn flex flex-wrap gap-2 mt-5">
                                        <span class="inline-block border border-white/25 text-white/70 text-[10px] px-3 py-1 rounded-full">%1 Kazanç</span>
                                        <span class="inline-block border border-white/25 text-white/70 text-[10px] px-3 py-1 rounded-full">Referans Bonusu</span>
                                        <span class="inline-block border border-white/25 text-white/70 text-[10px] px-3 py-1 rounded-full">Anında Kullanım</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Bar: Dots + Hashtag -->
                        <div class="absolute bottom-5 left-0 right-0">
                            <div class="container mx-auto px-6 lg:px-8 flex items-center justify-between">
                                <div></div>
                                <div class="flex items-center gap-6">
                                    <div class="flex items-center gap-2.5">
                                        <template x-for="(s, i) in 3" :key="i">
                                            <button @click="goToSlide(i)"
                                                class="dot-progress rounded-full transition-all duration-300"
                                                :class="currentSlide === i ? 'active w-8 h-2.5 bg-white' : 'w-2.5 h-2.5 bg-white/35 hover:bg-white/55'"
                                            ></button>
                                        </template>
                                    </div>
                                    <span class="text-white/50 text-[11px] font-medium tracking-wide hidden md:block">#YeniÇağınSanatGalerisi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script>
            function heroSlider() {
                return {
                    currentSlide: 0,
                    totalSlides: 3,
                    interval: null,
                    startAutoPlay() {
                        this.interval = setInterval(() => {
                            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                        }, 5000);
                    },
                    goToSlide(index) {
                        this.currentSlide = index;
                        clearInterval(this.interval);
                        this.startAutoPlay();
                    },
                    nextSlide() {
                        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                        clearInterval(this.interval);
                        this.startAutoPlay();
                    },
                    prevSlide() {
                        this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                        clearInterval(this.interval);
                        this.startAutoPlay();
                    }
                }
            }
        </script>
    </section>

    <!-- Artists Section -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-light text-brand-black100">Sanatçı<span class="font-semibold">larımız</span></h2>
                    <p class="text-gray-400 text-xs mt-1">Koleksiyonumuzdaki seçkin sanatçılar</p>
                </div>
                <a href="{{ route('artists') }}" class="text-xs text-brand-black100 hover:text-primary transition flex items-center gap-1 font-medium">
                    Tümünü Gör
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            <div class="relative" x-data="{ scrollContainer: null }" x-init="scrollContainer = $refs.artistScroll">
                <button @click="scrollContainer.scrollBy({left: -400, behavior: 'smooth'})"
                    class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 bg-white shadow-lg rounded-full w-10 h-10 flex items-center justify-center hover:shadow-xl hover:scale-105 transition hidden lg:flex">
                    <svg class="w-5 h-5 text-brand-black100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <div x-ref="artistScroll" class="flex space-x-4 overflow-x-auto pb-2 scrollbar-hide scroll-smooth">
                    @foreach($artists as $artist)
                        <a href="{{ route('artist.detail', $artist->slug) }}" class="flex-shrink-0 group" wire:key="artist-{{ $artist->id }}">
                            <div class="bg-white rounded-lg border border-gray-100 px-3 py-4 w-[120px] hover:shadow-md hover:border-gray-200 transition-all duration-300 text-center">
                                <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-gray-100 group-hover:border-primary transition-all mx-auto mb-3">
                                    @if($artist->avatar_url)
                                        <img src="{{ $artist->avatar_url }}" alt="{{ $artist->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center text-lg font-light text-gray-400">
                                            {{ mb_substr($artist->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <h3 class="font-medium text-brand-black100 text-[11px] leading-tight line-clamp-1">{{ $artist->name }}</h3>
                                <p class="text-gray-400 text-[10px] mt-0.5">{{ $artist->life_span }}</p>
                                @if($artist->artworks_count > 0)
                                    <p class="text-primary text-[9px] mt-1 font-medium">{{ $artist->artworks_count }} eser</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <button @click="scrollContainer.scrollBy({left: 400, behavior: 'smooth'})"
                    class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 bg-white shadow-lg rounded-full w-10 h-10 flex items-center justify-center hover:shadow-xl hover:scale-105 transition hidden lg:flex">
                    <svg class="w-5 h-5 text-brand-black100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- Featured Artworks -->
    @if($featuredArtworks->count() > 0)
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-light text-brand-black100">Öne Çıkan <span class="font-semibold">Eserler</span></h2>
                    <p class="text-gray-400 text-sm mt-1">Koleksiyonumuzdaki seçkin eserler</p>
                </div>
                <a href="{{ route('artworks') }}" class="text-xs text-gray-400 hover:text-brand-black100 transition link-underline pb-1 hidden md:block">Tümünü Gör</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
                @foreach($featuredArtworks as $artwork)
                    <div class="group" wire:key="featured-{{ $artwork->id }}">
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
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Latest Artworks -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-light text-brand-black100">Son Eklenen <span class="font-semibold">Eserler</span></h2>
                    <p class="text-gray-400 text-sm mt-1">Koleksiyonumuza yeni eklenen eserler</p>
                </div>
                <a href="{{ route('artworks') }}" class="text-xs text-gray-400 hover:text-brand-black100 transition link-underline pb-1 hidden md:block">Tümünü Gör</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-8">
                @foreach($latestArtworks as $artwork)
                    <div class="group" wire:key="latest-{{ $artwork->id }}">
                        <a href="{{ route('artwork.detail', $artwork->slug) }}" class="block">
                            <div class="relative bg-white overflow-hidden aspect-square mb-3">
                                @if($artwork->is_sold)
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-[9px] px-2 py-0.5 z-10 uppercase tracking-wider">Satıldı</span>
                                @elseif($artwork->is_reserved)
                                    <span class="absolute top-2 left-2 bg-amber-500 text-white text-[9px] px-2 py-0.5 z-10 uppercase tracking-wider">Rezerve</span>
                                @endif
                                @if($artwork->first_image)
                                    <img src="{{ $artwork->first_image_url }}" alt="{{ $artwork->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <h3 class="font-medium text-brand-black100 text-xs truncate">{{ $artwork->artist->name }}</h3>
                        <p class="text-gray-400 text-[10px] mt-0.5 truncate">{{ $artwork->title }}</p>
                        <p class="font-medium text-brand-black100 text-xs mt-1">{{ $artwork->formatted_price_tl }}</p>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-10">
                <a href="{{ route('artworks') }}" class="inline-flex items-center border border-brand-black100 text-brand-black100 px-8 py-3 text-xs font-medium hover:bg-brand-black100 hover:text-white transition-all">
                    Tüm Eserleri Görüntüle
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    @if($blogPosts->count() > 0)
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-light text-brand-black100">Sanat <span class="font-semibold">Yazıları</span></h2>
                    <p class="text-gray-400 text-sm mt-1">Sanat dünyasından haberler ve yazılar</p>
                </div>
                <a href="{{ route('blog') }}" class="text-xs text-gray-400 hover:text-brand-black100 transition link-underline pb-1 hidden md:block">Tümünü Gör</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($blogPosts as $post)
                    <article class="group" wire:key="blog-{{ $post->id }}">
                        <a href="{{ route('blog.detail', $post->slug) }}" class="block">
                            <div class="aspect-[16/10] overflow-hidden bg-gray-100 mb-3">
                                <img
                                    src="{{ $post->image_url }}"
                                    alt="{{ $post->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                    onerror="this.src='https://via.placeholder.com/400x250/f3f4f6/9ca3af?text=BeArtShare'"
                                >
                            </div>
                            <div>
                                @if($post->category)
                                    <span class="text-[10px] font-medium text-primary uppercase tracking-wider">{{ $post->category->title }}</span>
                                @endif
                                <h3 class="text-sm font-semibold text-brand-black100 mt-1 group-hover:text-primary transition line-clamp-2 leading-snug">
                                    {{ $post->title }}
                                </h3>
                                <p class="text-xs text-gray-400 mt-2">{{ $post->created_at->translatedFormat('d F Y') }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="text-center mt-10 md:hidden">
                <a href="{{ route('blog') }}" class="inline-flex items-center border border-brand-black100 text-brand-black100 px-8 py-3 text-xs font-medium hover:bg-brand-black100 hover:text-white transition-all">
                    Tüm Yazıları Görüntüle
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- Neden BeArtShare? -->
    <section class="py-20 bg-gray-50 relative overflow-hidden">
        {{-- Dekoratif arka plan --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-primary/[0.03] rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-brand-black100/[0.03] rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-14">
                <h2 class="text-2xl font-light text-brand-black100">Neden <span class="font-semibold">BeArtShare?</span></h2>
                <p class="text-gray-400 text-sm mt-2">Sanat yolculuğunuzda yanınızdayız</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                {{-- Güvenli Alışveriş --}}
                <div class="bg-white border border-gray-100 p-8 text-center group hover:shadow-xl hover:border-gray-200 hover:-translate-y-1 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 flex items-center justify-center group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-green-100/50 transition-all duration-300">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-brand-black100 mb-2">Güvenli Alışveriş</h3>
                    <p class="text-gray-400 text-xs leading-relaxed mb-4">Tüm eserler orijinallik garantisi ile teslim edilir. Uzman ekibimiz her eseri titizlikle incelemektedir.</p>
                    <div class="flex items-center justify-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-[10px] text-green-600 bg-green-50 px-2.5 py-1 font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Orijinallik Garantisi
                        </span>
                        <span class="inline-flex items-center gap-1 text-[10px] text-green-600 bg-green-50 px-2.5 py-1 font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Uzman İnceleme
                        </span>
                    </div>
                </div>

                {{-- Özel Paketleme & Kargo --}}
                <div class="bg-white border border-gray-100 p-8 text-center group hover:shadow-xl hover:border-gray-200 hover:-translate-y-1 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 flex items-center justify-center group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-blue-100/50 transition-all duration-300">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-brand-black100 mb-2">Özel Paketleme & Kargo</h3>
                    <p class="text-gray-400 text-xs leading-relaxed mb-4">Eserler profesyonel sanat paketleme yöntemleri ile özel olarak hazırlanır ve sigortalı kargo ile teslim edilir.</p>
                    <div class="flex items-center justify-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-[10px] text-blue-600 bg-blue-50 px-2.5 py-1 font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Sigortalı Kargo
                        </span>
                        <span class="inline-flex items-center gap-1 text-[10px] text-blue-600 bg-blue-50 px-2.5 py-1 font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Profesyonel Ambalaj
                        </span>
                    </div>
                </div>

                {{-- ArtPuan Kazanın --}}
                <div class="bg-white border border-gray-100 p-8 text-center group hover:shadow-xl hover:border-gray-200 hover:-translate-y-1 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 flex items-center justify-center group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-amber-100/50 transition-all duration-300">
                        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-brand-black100 mb-2">ArtPuan Kazanın</h3>
                    <p class="text-gray-400 text-xs leading-relaxed mb-4">Her alışverişinizde ArtPuan kazanın, sonraki alışverişlerinizde indirim olarak kullanın.</p>
                    <div class="flex items-center justify-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-[10px] text-primary bg-amber-50 px-2.5 py-1 font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            %1 Geri Kazanım
                        </span>
                        <span class="inline-flex items-center gap-1 text-[10px] text-primary bg-amber-50 px-2.5 py-1 font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Referans Bonusu
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
