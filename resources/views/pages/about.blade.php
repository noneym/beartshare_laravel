<x-layouts.app
    title="Hakkımızda | BeArtShare - Yeni Çağın Sanat Galerisi"
    metaDescription="BeArtShare, koleksiyonerleri seçkin sanat eserleriyle güvenilir ve şeffaf bir ortamda buluşturan online sanat platformudur. Misyonumuz, vizyonumuz ve ekibimiz."
    metaKeywords="beartshare hakkında, online sanat galerisi, sanat platformu, misyon, vizyon, türk sanat galerisi"
    :flush-footer="true"
>
    @php
        // Hero duvarı: sitedeki gerçek eserler (öne çıkanlar önce)
        $wallWorks = \App\Models\Artwork::active()
            ->whereNotNull('images')
            ->where('images', '!=', '[]')
            ->orderByDesc('is_featured')
            ->latest()
            ->take(2)
            ->get();

        // Ekip görselleri R2'de about/team/, Thumbor 'team' boyutu
        $founders = [
            ['name' => 'Sinan Aydın', 'role' => 'Co-Founder', 'image' => 'about/team/sinan-aydin.jpg'],
            ['name' => 'Osman Nuri İyem', 'role' => 'Co-Founder', 'image' => 'about/team/osman-nuri-iyem.jpg'],
        ];
        $team = [
            ['name' => 'Gizem Kahya İyem', 'role' => 'Sanat Danışmanı', 'image' => 'about/team/gizem-kahya-iyem.jpg'],
            ['name' => 'Doğa Atçı', 'role' => 'Sanat Danışmanı', 'image' => 'about/team/doga-atci-2.jpg'],
            ['name' => 'Berk Say', 'role' => 'Yazılım Danışmanı', 'image' => 'about/team/berk-say.jpg'],
        ];

        $reasons = [
            ['icon' => 'ph-frame-corners', 'title' => 'Küratöryel seçki', 'text' => 'Galericilik deneyimiyle oluşturulan, özenle seçilmiş yerli ve uluslararası sanatçılar.'],
            ['icon' => 'ph-magnifying-glass', 'title' => 'Güvenilir değerlendirme', 'text' => 'Eserler mevcut bilgi ve belgeler doğrultusunda incelenir; gerektiğinde bağımsız uzman görüşüne başvurulur.'],
            ['icon' => 'ph-lock-simple', 'title' => 'Güvenli ödeme', 'text' => 'SSL korumalı altyapı ile güvenli ve şeffaf ödeme deneyimi.'],
            ['icon' => 'ph-package', 'title' => 'Sigortalı teslimat', 'text' => 'Her eser profesyonel paketleme standartlarında hazırlanır ve sigortalı kargo ile teslim edilir.'],
            ['icon' => 'ph-users', 'title' => 'Koleksiyonerden koleksiyonere', 'text' => 'Koleksiyonunuzdaki eserleri düşük komisyonla, güvenli ve şeffaf şekilde yeni koleksiyonerlerle buluşturun.', 'url' => route('eser-kabulu')],
            ['icon' => 'ph-plus-circle', 'title' => 'ArtPuan® ayrıcalığı', 'text' => 'Alımlarınızdan ve referanslarınızdan ArtPuan® kazanın, sonraki alımlarınızda kullanın.', 'url' => route('artpuan'), 'artpuan' => true],
        ];
    @endphp

    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/light/style.css">

    {{-- Sayfa teması: koyu galeri duvarı. Köşe kuralı: tamamen keskin (radius 0). --}}
    <style>
        .about-wall { background-color: #3a352e; background-image: radial-gradient(ellipse 60% 70% at 78% 20%, rgba(255,236,200,0.10), transparent 70%); }
        .about-frame { background: #1f1c18; padding: 10px; box-shadow: 0 30px 60px -25px rgba(10,8,5,0.75); }
        .about-mat { background: #efe9dd; padding: 9%; }
        @media (prefers-reduced-motion: no-preference) {
            .about-rise { opacity: 0; transform: translateY(12px); animation: aboutRise .8s cubic-bezier(.16,1,.3,1) forwards; }
            @keyframes aboutRise { to { opacity: 1; transform: none; } }
        }
    </style>

    <div class="about-wall text-[#f1ece3]">
        <!-- Hero -->
        <section class="container mx-auto px-4 pt-14 pb-16 lg:pt-20 lg:pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-14 lg:gap-10 items-center">
                <div class="lg:col-span-7">
                    <p class="about-rise text-xs tracking-[0.3em] uppercase text-[#d8cdb9]">Hakkımızda</p>
                    <div class="about-rise w-14 h-px bg-primary/70 mt-4" style="animation-delay:.05s"></div>
                    <h1 class="about-rise text-4xl md:text-5xl font-medium leading-[1.1] tracking-tight mt-7 max-w-3xl" style="animation-delay:.1s">
                        Koleksiyonerler için güvenilir bir sanat platformu
                    </h1>
                    <p class="about-rise text-lg md:text-xl text-[#e6dfd3] leading-relaxed mt-7 max-w-[46ch]" style="animation-delay:.2s">
                        BeArtShare, koleksiyonerleri seçkin sanat eserleriyle güvenilir ve şeffaf bir ortamda buluşturan online sanat platformudur.
                    </p>
                </div>

                {{-- Duvardaki eserler: gerçek eser görselleri, çerçeve + paspartu --}}
                <div class="lg:col-span-5 about-rise" style="animation-delay:.25s">
                    @if($wallWorks->count() === 2)
                        <div class="relative grid grid-cols-12 items-end gap-5 max-w-md mx-auto lg:max-w-none">
                            @foreach($wallWorks as $i => $work)
                                <a href="{{ route('artwork.detail', $work->slug) }}"
                                   class="{{ $i === 0 ? 'col-span-7' : 'col-span-5 mb-10' }} about-frame block group">
                                    <div class="about-mat">
                                        <img src="{{ $work->list_image_url }}" alt="{{ $work->title }}{{ $work->artist ? ', ' . $work->artist->name : '' }}"
                                             class="w-full {{ $i === 0 ? 'aspect-[4/5]' : 'aspect-square' }} object-cover transition duration-700 group-hover:scale-[1.02]"
                                             {{ $i === 0 ? 'fetchpriority=high' : 'loading=lazy' }}>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Seçki ve değerlendirme + Misyon / Vizyon -->
        <section class="container mx-auto px-4 pb-16 lg:pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
                <div class="lg:col-span-7 space-y-5 text-[15px] leading-relaxed text-[#cfc6b8] max-w-[65ch]">
                    <p>Türkiye'nin önde gelen sanatçılarından uluslararası Blue Chip sanatçılara uzanan, özenle oluşturulmuş seçkimiz; yıllara dayanan galericilik deneyimi ve sanat piyasası uzmanlığıyla bir araya getirilmektedir.</p>
                    <p>Platformumuzda yer alan her eser, mevcut bilgi ve belgeler doğrultusunda titizlikle değerlendirilir. Gerekli görülen durumlarda bağımsız uzman görüşlerinden yararlanılarak güvenilir ve şeffaf bir koleksiyon deneyimi sağlanır.</p>
                </div>
            </div>

            <div class="mt-14 pt-12 border-t border-[#f1ece3]/10 grid grid-cols-1 md:grid-cols-2 md:divide-x divide-[#f1ece3]/10">
                @foreach([
                    ['icon' => 'ph-target', 'title' => 'Misyonumuz', 'text' => 'Koleksiyonerleri nitelikli sanat eserleriyle güvenilir bir ortamda buluştururken, sanatçılar ve eserleri için uzun vadeli değer yaratan sürdürülebilir bir platform oluşturmak.'],
                    ['icon' => 'ph-eye', 'title' => 'Vizyonumuz', 'text' => "Türkiye'nin referans gösterilen online sanat platformlarından biri olarak, yerel ve uluslararası sanatçıları koleksiyonerlerle buluşturan güvenilir bir ekosistem oluşturmak."],
                ] as $i => $block)
                    <div class="flex gap-6 {{ $i === 0 ? 'md:pr-12 pb-10 md:pb-0' : 'md:pl-12 pt-10 md:pt-0 border-t md:border-t-0 border-[#f1ece3]/10' }}">
                        <span class="flex-shrink-0 w-16 h-16 rounded-full border border-primary/60 text-primary flex items-center justify-center">
                            <i class="ph-light {{ $block['icon'] }} text-3xl"></i>
                        </span>
                        <div>
                            <h2 class="text-lg font-semibold tracking-wide">{{ $block['title'] }}</h2>
                            <p class="text-sm text-[#cfc6b8] leading-relaxed mt-2 max-w-[52ch]">{{ $block['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <!-- Biz Kimiz -->
    <section class="bg-[#2f2b25] text-[#f1ece3] py-16 lg:py-24">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-10 items-start">
                <div class="lg:col-span-6 lg:sticky lg:top-28">
                    <h2 class="text-3xl md:text-4xl font-medium tracking-tight">Biz kimiz</h2>
                    <div class="w-14 h-px bg-primary/70 mt-5"></div>
                    <p class="text-[15px] text-[#cfc6b8] leading-relaxed mt-6 max-w-[52ch]">
                        BeArtShare A.Ş. Sinan Aydın ve Osman Nuri İyem tarafından kurulmuştur. Av. Sinan Aydın müzayede evleri ve sanat galerilerine danışmanlık vermiş, sanat koleksiyoneri bir avukattır. Osman Nuri İyem Evin Sanat Galerisi'nin sahibi, aynı zamanda fotoğraf sanatçısı ve küratördür. Her ikisi de yıllardır sanat piyasasında güvenilirlikleriyle önemli bir yer edinmiştir.
                    </p>
                    <p class="text-[15px] text-[#cfc6b8] leading-relaxed mt-4 max-w-[52ch]">
                        Ekibimizde ayrıca Gizem Kahya İyem ve Doğa Atçı sanat danışmanlarımız, Berk Say ise start-up yazılım danışmanımız olarak yer almaktadır.
                    </p>
                </div>

                <div class="lg:col-span-5 lg:col-start-8">
                    {{-- Kurucular: büyük portreler --}}
                    <div class="grid grid-cols-2 gap-4 md:gap-6 max-w-md mx-auto lg:mx-0">
                        @foreach($founders as $member)
                            <figure>
                                <div class="aspect-[3/4] overflow-hidden bg-[#3a352e]">
                                    <img src="{{ \App\Support\ImageUrl::make($member['image'], 'team') }}" alt="{{ $member['name'] }}"
                                         loading="lazy" class="w-full h-full object-cover">
                                </div>
                                <figcaption class="mt-4">
                                    <p class="text-base font-medium">{{ $member['name'] }}</p>
                                    <p class="text-sm text-primary/90 mt-0.5">{{ $member['role'] }}</p>
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>

                    {{-- Ekip --}}
                    <div class="grid grid-cols-3 gap-4 md:gap-5 mt-10 pt-10 border-t border-[#f1ece3]/10 max-w-md mx-auto lg:mx-0">
                        @foreach($team as $member)
                            <figure>
                                <div class="aspect-[3/4] overflow-hidden bg-[#3a352e]">
                                    <img src="{{ \App\Support\ImageUrl::make($member['image'], 'team') }}" alt="{{ $member['name'] }}"
                                         loading="lazy" class="w-full h-full object-cover">
                                </div>
                                <figcaption class="mt-3">
                                    <p class="text-sm font-medium">{{ $member['name'] }}</p>
                                    <p class="text-xs text-[#b9b0a2] mt-0.5">{{ $member['role'] }}</p>
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Neden BeArtShare -->
    <section class="about-wall text-[#f1ece3] py-16 lg:py-24">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-medium tracking-tight text-center">Neden BeArtShare?</h2>
            <div class="w-14 h-px bg-primary/70 mx-auto mt-5"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 mt-12 border-t border-[#f1ece3]/10 xl:border-t-0">
                @foreach($reasons as $reason)
                    @php $tag = isset($reason['url']) ? 'a' : 'div'; @endphp
                    <{{ $tag }} @if(isset($reason['url'])) href="{{ $reason['url'] }}" @endif
                        class="group block text-center px-6 py-10 border-b border-[#f1ece3]/10 sm:border-r xl:border-b-0 xl:last:border-r-0 {{ isset($reason['url']) ? 'hover:bg-white/[0.03] transition' : '' }}">
                        <i class="ph-light {{ $reason['icon'] }} text-[44px] {{ !empty($reason['artpuan']) ? 'text-artpuan' : 'text-primary' }}"></i>
                        <h3 class="text-sm font-semibold uppercase tracking-wide mt-5 {{ !empty($reason['artpuan']) ? 'text-artpuan' : '' }}">{{ $reason['title'] }}</h3>
                        <p class="text-[13px] text-[#cfc6b8] leading-relaxed mt-3 max-w-[30ch] mx-auto">{{ $reason['text'] }}</p>
                    </{{ $tag }}>
                @endforeach
            </div>

            <div class="mt-16 flex flex-col sm:flex-row items-center justify-center gap-5 text-center">
                <p class="text-[15px] text-[#e6dfd3]">Koleksiyonunuza yeni bir eser eklemeye hazır mısınız?</p>
                <a href="{{ route('artworks') }}" class="inline-flex items-center gap-2 bg-[#f1ece3] text-[#2f2b25] px-7 py-3 text-sm font-medium hover:bg-white active:scale-[0.98] transition">
                    Eserleri Keşfet <i class="ph-light ph-arrow-right text-base"></i>
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
