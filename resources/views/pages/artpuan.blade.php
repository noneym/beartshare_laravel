<x-layouts.app
    title="ArtPuan® Sadakat Programı | BeArtShare - Sanat Alışverişinde Kazan"
    metaDescription="ArtPuan® ile sanat alışverişlerinizde puan kazanın, indirimlerden yararlanın. BeArtShare sadakat programı avantajlarını keşfedin."
    metaKeywords="artpuan, sadakat programı, sanat alışverişi puan, beartshare puan, sanat indirimi, referans programı"
>
    @php
        // Hero kolajı: satıştaki, görseli olan eserlerden (öne çıkanlar önce)
        $heroWorks = \App\Models\Artwork::with('artist')
            ->available()
            ->whereNotNull('images')
            ->where('images', '!=', '[]')
            ->orderByDesc('is_featured')
            ->latest()
            ->take(3)
            ->get();
    @endphp

    {{-- Phosphor ikonları (yalnızca bu sayfa) --}}
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/fill/style.css">

    {{-- Köşe kuralı: kart ve görseller rounded-2xl, buton / girdi / ikon rozetleri rounded-full --}}
    <style>
        @media (prefers-reduced-motion: no-preference) {
            .ap-rise { opacity: 0; transform: translateY(14px); animation: apRise .7s cubic-bezier(.16,1,.3,1) forwards; }
            @keyframes apRise { to { opacity: 1; transform: none; } }
        }
    </style>

    <!-- Hero -->
    <section class="relative overflow-hidden" style="background: linear-gradient(135deg, #e3efda 0%, #cfe3c1 55%, #bcd8a8 100%);">
        <div class="container mx-auto px-4 pt-8 pb-14 md:pt-10 md:pb-20">
            <nav class="text-xs text-artpuan-ink/80 mb-8 md:mb-12">
                <a href="{{ route('home') }}" class="hover:text-artpuan-ink">Ana Sayfa</a>
                <span class="mx-2">/</span>
                <span class="text-artpuan-ink">ArtPuan&reg;</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-10 items-center">
                <div class="lg:col-span-6">
                    <h1 class="ap-rise flex items-center gap-4 text-5xl md:text-6xl font-semibold text-brand-black100 tracking-tight leading-none">
                        <span>ArtPuan<sup class="text-2xl md:text-3xl font-normal align-super">&reg;</sup></span>
                        <span class="inline-flex w-12 h-12 md:w-14 md:h-14 rounded-full border-2 border-artpuan-ink text-artpuan-ink items-center justify-center">
                            <i class="ph ph-plus text-2xl md:text-3xl"></i>
                        </span>
                    </h1>
                    <p class="ap-rise text-2xl md:text-3xl font-medium text-brand-black100 leading-snug mt-6 max-w-lg" style="animation-delay:.08s">
                        Sanat alın, kazanın, koleksiyonunuza değer katın.
                    </p>
                    <p class="ap-rise text-[15px] text-brand-black100/70 leading-relaxed mt-5 max-w-[52ch]" style="animation-delay:.16s">
                        BeArtShare'de eser alımlarınızdan, satışlarınızdan ve davet ettiğiniz üyelerin alımlarından %1 ArtPuan&reg; kazanırsınız.
                    </p>
                    <div class="ap-rise flex flex-wrap items-center gap-3 mt-8" style="animation-delay:.24s">
                        @auth
                            <a href="#hesap" class="inline-flex items-center gap-2 bg-artpuan-ink text-white pl-6 pr-5 py-3 rounded-full text-sm font-medium hover:bg-[#3b6127] active:scale-[0.98] transition">
                                ArtPuan&reg; Hesabım <i class="ph ph-arrow-down text-base"></i>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-artpuan-ink text-white pl-6 pr-5 py-3 rounded-full text-sm font-medium hover:bg-[#3b6127] active:scale-[0.98] transition">
                                Ücretsiz Üye Ol <i class="ph ph-arrow-right text-base"></i>
                            </a>
                        @endauth
                        <a href="#nasil" class="inline-flex items-center gap-2 border border-artpuan-ink/30 text-artpuan-ink px-6 py-3 rounded-full text-sm font-medium hover:bg-white/40 active:scale-[0.98] transition">
                            Nasıl Çalışır?
                        </a>
                    </div>
                </div>

                {{-- Gerçek eserlerden kolaj + örnek kazanç kartı --}}
                <div class="lg:col-span-6 ap-rise" style="animation-delay:.2s">
                    @if($heroWorks->count() >= 3)
                        <div class="grid grid-cols-5 grid-rows-2 gap-3 md:gap-4 h-[320px] md:h-[400px]">
                            @foreach($heroWorks as $i => $work)
                                <a href="{{ route('artwork.detail', $work->slug) }}"
                                   class="{{ $i === 0 ? 'col-span-3 row-span-2' : 'col-span-2' }} block bg-white p-2 md:p-3 rounded-2xl shadow-[0_18px_40px_-18px_rgba(71,115,47,0.45)] hover:-translate-y-1 transition duration-500 overflow-hidden">
                                    <img src="{{ $work->list_image_url }}" alt="{{ $work->title }}, {{ $work->artist->name ?? '' }}"
                                         class="w-full h-full object-cover rounded-xl bg-artpuan-soft" {{ $i === 0 ? 'fetchpriority=high' : 'loading=lazy' }}>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <img src="{{ \App\Support\ImageUrl::make('site/hero/sanal-sergi-v2.webp', 1440) }}" alt="BeArtShare sanal sergi salonu" class="w-full h-[320px] md:h-[400px] object-cover rounded-2xl">
                    @endif

                    <div class="mt-4 md:mt-5 md:ml-auto bg-white rounded-2xl shadow-[0_20px_50px_-20px_rgba(71,115,47,0.5)] px-5 py-4 flex items-center gap-4 md:max-w-sm">
                        <span class="flex-shrink-0 w-11 h-11 rounded-full bg-artpuan text-white flex items-center justify-center">
                            <i class="ph ph-plus text-xl"></i>
                        </span>
                        <p class="text-sm text-brand-black100 leading-snug">
                            10.000 TL'lik eser alımında <strong class="text-artpuan-ink font-semibold">100 ArtPuan&reg;</strong> kazanırsınız.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Nasıl Çalışır -->
    <section id="nasil" class="py-16 md:py-24 bg-white scroll-mt-24">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-semibold text-brand-black100 text-center tracking-tight">ArtPuan&reg; nasıl çalışır?</h2>
            <div class="w-12 h-0.5 bg-artpuan mx-auto mt-5"></div>

            <div class="relative grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8 max-w-5xl mx-auto mt-14">
                {{-- Adımlar arası çizgi (yalnızca masaüstü) --}}
                <div class="hidden md:block absolute top-11 left-[16.6%] right-[16.6%] h-px bg-artpuan/40"></div>

                @foreach([
                    ['icon' => 'ph-shopping-cart-simple', 'title' => 'Eser alın', 'text' => "BeArtShare üzerinden aldığınız her eserde tutarın %1'i kadar ArtPuan® hesabınıza yüklenir."],
                    ['icon' => 'ph-user-plus', 'title' => 'Davet edin', 'text' => "Referans kodunuzla davet ettiğiniz üyelerin alımlarından siz de %1 ArtPuan® kazanırsınız."],
                    ['icon' => 'ph-credit-card', 'title' => 'Kullanın', 'text' => "Biriken ArtPuan®'larınızı sonraki eser alımlarınızda ödeme adımında indirim olarak kullanın."],
                ] as $step)
                    <div class="relative text-center group">
                        <div class="relative w-[88px] h-[88px] mx-auto rounded-full bg-artpuan-soft ring-8 ring-white flex items-center justify-center group-hover:bg-artpuan/20 transition duration-300">
                            <i class="ph {{ $step['icon'] }} text-4xl text-artpuan-ink"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-brand-black100 mt-6">{{ $step['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed mt-2 max-w-[30ch] mx-auto">{{ $step['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Daha fazla kazanın + örnek hesap -->
    <section class="py-16 md:py-24 bg-stone-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center max-w-6xl mx-auto">
                <div class="lg:col-span-6">
                    <h2 class="text-3xl md:text-4xl font-semibold text-brand-black100 tracking-tight leading-tight max-w-md">ArtPuan&reg; ile daha fazla kazanın</h2>
                    <ul class="mt-8 space-y-4">
                        @foreach([
                            "Her eser alımınızda %1 ArtPuan® kazanırsınız.",
                            "BeArtShare aracılığıyla sattığınız eserlerden de %1 kazanırsınız.",
                            "Davet ettiğiniz üyelerin alımlarından ömür boyu %1 kazanırsınız.",
                            "Puanlarınızın üst sınırı ve son kullanma tarihi yoktur.",
                        ] as $item)
                            <li class="flex items-start gap-3 text-[15px] text-brand-black100/80 leading-relaxed">
                                <i class="ph-fill ph-plus-circle text-2xl text-artpuan flex-shrink-0 -mt-0.5"></i>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-gray-500 mt-6 max-w-[60ch] leading-relaxed">
                        Davet ettiğiniz üye kendi adıyla ayrı bir hesap açar. ArtPuan&reg; bakiyeleri kişiye özeldir, hesaplar ve bakiyeler birleşmez.
                    </p>
                </div>

                <div class="lg:col-span-5 lg:col-start-8">
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-[0_24px_60px_-30px_rgba(71,115,47,0.45)]">
                        <h3 class="text-base font-semibold text-brand-black100 text-center">Örnek kazanç hesabı</h3>

                        <div class="mt-5 bg-artpuan-soft rounded-2xl px-5 py-5 text-center">
                            <p class="text-xs text-gray-500">10.000 TL'lik bir eser alımında</p>
                            <p class="text-4xl font-semibold text-artpuan-ink mt-1 tabular-nums">100</p>
                            <p class="text-xs text-artpuan-ink/80 mt-0.5">ArtPuan&reg; kazanırsınız</p>
                        </div>

                        <div class="relative flex justify-center -my-3 z-[1]">
                            <span class="w-9 h-9 rounded-full bg-artpuan text-white flex items-center justify-center ring-4 ring-white">
                                <i class="ph ph-plus text-lg"></i>
                            </span>
                        </div>

                        <div class="bg-artpuan-soft rounded-2xl px-5 py-5 text-center">
                            <p class="text-xs text-gray-500">Davet ettiğiniz üye aynı tutarda alım yaptığında</p>
                            <p class="text-4xl font-semibold text-artpuan-ink mt-1 tabular-nums">100</p>
                            <p class="text-xs text-artpuan-ink/80 mt-0.5">ek ArtPuan&reg; kazanırsınız</p>
                        </div>

                        <div class="mt-6 pt-5 border-t border-gray-100 flex items-baseline justify-between">
                            <span class="text-sm text-gray-500">Toplam</span>
                            <span class="text-2xl font-semibold text-brand-black100 tabular-nums">200 <span class="text-base font-medium text-artpuan-ink">ArtPuan&reg;</span></span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-right">1 ArtPuan&reg; = 1 TL</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @auth
        @php
            $authUser = auth()->user();
            $referralsCount = \App\Models\User::where('referred_by', $authUser->id)->count();
        @endphp
        <!-- Üyenin ArtPuan hesabı -->
        <section id="hesap" class="py-16 md:py-20 bg-white scroll-mt-24">
            <div class="container mx-auto px-4 max-w-6xl">
                <h2 class="text-3xl md:text-4xl font-semibold text-brand-black100 tracking-tight">ArtPuan&reg; hesabınız</h2>
                <p class="text-sm text-gray-500 mt-2">Merhaba {{ $authUser->name }}, güncel durumunuz aşağıda.</p>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
                    <div class="bg-artpuan-ink text-white rounded-2xl p-6 flex flex-col justify-between min-h-[170px]">
                        <p class="text-sm text-white/75">Bakiyeniz</p>
                        <div>
                            <p class="text-4xl font-semibold tabular-nums">{{ number_format($authUser->art_puan, 0, ',', '.') }}</p>
                            <p class="text-sm text-white/75 mt-1">ArtPuan&reg; (1 AP = 1 TL)</p>
                        </div>
                    </div>

                    <div class="bg-stone-50 rounded-2xl p-6 flex flex-col justify-between min-h-[170px]">
                        <p class="text-sm text-gray-500">Davet ettiğiniz üyeler</p>
                        <div class="flex items-end justify-between">
                            <p class="text-4xl font-semibold text-brand-black100 tabular-nums">{{ $referralsCount }}</p>
                            <i class="ph ph-users-three text-3xl text-artpuan-ink"></i>
                        </div>
                    </div>

                    <div class="md:col-span-2 bg-artpuan-soft rounded-2xl p-6 flex flex-col justify-between gap-4" x-data="{ copied: false }">
                        <div class="flex items-center justify-between gap-4">
                            <label for="ap-ref-link" class="text-sm text-artpuan-ink font-medium">Referans linkiniz</label>
                            <span class="text-xs text-gray-500">Kod: <span class="font-mono text-artpuan-ink font-medium">{{ $authUser->referral_code }}</span></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <input id="ap-ref-link" type="text" value="{{ $authUser->referral_link }}" readonly
                                   class="flex-1 min-w-0 bg-white text-sm text-brand-black100 px-4 py-2.5 rounded-full border border-artpuan/40 focus:outline-none focus:ring-2 focus:ring-artpuan-ink/40">
                            <button type="button" @click="navigator.clipboard.writeText(@js($authUser->referral_link)); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="flex-shrink-0 bg-artpuan-ink text-white px-5 py-2.5 rounded-full text-sm font-medium hover:bg-[#3b6127] active:scale-[0.98] transition">
                                <span x-show="!copied">Kopyala</span>
                                <span x-show="copied" x-cloak>Kopyalandı</span>
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 mr-1">Paylaş:</span>
                            <a href="https://wa.me/?text={{ urlencode('BeArtShare\'de harika eserler keşfet! ' . $authUser->referral_link) }}"
                               target="_blank" rel="noopener" title="WhatsApp ile paylaş"
                               class="w-9 h-9 rounded-full bg-white text-[#1f9e4d] flex items-center justify-center hover:bg-[#25D366] hover:text-white transition">
                                <i class="ph ph-whatsapp-logo text-lg"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($authUser->referral_link) }}"
                               target="_blank" rel="noopener" title="Facebook'ta paylaş"
                               class="w-9 h-9 rounded-full bg-white text-[#1877F2] flex items-center justify-center hover:bg-[#1877F2] hover:text-white transition">
                                <i class="ph ph-facebook-logo text-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('profile', 'artpuan') }}" class="inline-flex items-center gap-2 mt-6 text-sm font-medium text-artpuan-ink hover:underline">
                    ArtPuan&reg; hareketlerim <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </section>
    @endauth

    <!-- Nerede kullanılır -->
    <section class="py-16 md:py-24 {{ auth()->check() ? 'bg-stone-50' : 'bg-white' }}">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center max-w-6xl mx-auto">
                <div class="lg:col-span-7 order-2 lg:order-1">
                    <img src="{{ \App\Support\ImageUrl::make('site/hero/sanal-sergi-v2.webp', 1440) }}" alt="BeArtShare sanal sergi salonunda satıştaki eserler"
                         class="w-full aspect-[16/10] object-cover rounded-2xl" loading="lazy">
                </div>
                <div class="lg:col-span-5 order-1 lg:order-2">
                    <h2 class="text-3xl md:text-4xl font-semibold text-brand-black100 tracking-tight leading-tight">ArtPuan&reg;'larınızı nerede kullanabilirsiniz?</h2>
                    <p class="text-[15px] text-gray-600 leading-relaxed mt-5 max-w-[48ch]">
                        Biriken ArtPuan&reg;'larınızı platformdaki tüm eser alımlarında, ödeme adımında indirim olarak kullanabilirsiniz.
                    </p>
                    <a href="{{ route('artworks') }}" class="inline-flex items-center gap-2 mt-8 bg-artpuan-ink text-white pl-6 pr-5 py-3 rounded-full text-sm font-medium hover:bg-[#3b6127] active:scale-[0.98] transition">
                        Eserleri Keşfet <i class="ph ph-arrow-right text-base"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SSS şeridi -->
    <section class="pb-16 md:pb-20 {{ auth()->check() ? 'bg-stone-50' : 'bg-white' }}">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="bg-artpuan-soft rounded-2xl px-6 py-6 md:px-8 flex flex-col md:flex-row md:items-center gap-5 md:gap-8">
                <span class="flex-shrink-0 w-11 h-11 rounded-full bg-artpuan text-white flex items-center justify-center">
                    <i class="ph ph-question text-xl"></i>
                </span>
                <p class="flex-1 text-sm text-brand-black100/80 leading-relaxed">
                    ArtPuan&reg; kazanmak ve kullanmak için üye girişi yapmanız yeterlidir. Diğer sorularınız için sıkça sorulan sorulara göz atabilir veya
                    <a href="tel:02122906413" class="text-artpuan-ink font-medium hover:underline whitespace-nowrap">0212 290 64 13</a> numarasını arayabilirsiniz.
                </p>
                <a href="{{ route('faq') }}" class="inline-flex items-center gap-2 text-sm font-medium text-artpuan-ink hover:underline whitespace-nowrap">
                    Sıkça Sorulan Sorular <i class="ph ph-arrow-right"></i>
                </a>
            </div>

            <p class="text-gray-400 text-[11px] leading-relaxed mt-8 text-center max-w-4xl mx-auto">
                * ArtPuan&reg; oranları ve kullanım şartları ilgili eserin sayfasında belirtilecektir. ArtPuan&reg;'lar nakdi olarak ödenmeyecek olup, sadece sitemizden eser alımında kullanılabilecektir. ArtPuan&reg;'lar şahsa özel olup başkalarına devredilemeyecektir. BeArtShare, ArtPuan&reg; programı koşullarında değişiklik yapma hakkını saklı tutar.
            </p>
        </div>
    </section>
</x-layouts.app>
