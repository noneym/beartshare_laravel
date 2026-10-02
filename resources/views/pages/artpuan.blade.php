<x-layouts.app
    title="ArtPuan® Sadakat Programı | BeArtShare - Sanat Alışverişinde Kazan"
    metaDescription="ArtPuan® ile sanat alışverişlerinizde puan kazanın, indirimlerden yararlanın. BeArtShare sadakat programı avantajlarını keşfedin."
    metaKeywords="artpuan, sadakat programı, sanat alışverişi puan, beartshare puan, sanat indirimi, referans programı"
>
    <x-page-header title="ArtPuan®" subtitle="Sanat galericiliğinde bir ilk. Her alışverişinizde puan kazanın, sanat koleksiyonunuzu büyütün." />

    {{-- Nasıl çalışır: 3 adım, numarasız, fiil-isim --}}
    <section class="py-14 lg:py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100 mb-10">Nasıl çalışır?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-10 gap-y-8">
                <div class="border-t border-brand-black100 pt-5">
                    <i class="ph ph-user-plus text-3xl text-primary"></i>
                    <h3 class="mt-4 text-lg font-medium text-brand-black100">Üye olun</h3>
                    <p class="mt-2 text-gray-600 leading-relaxed">BeArtShare'e ücretsiz üye olduğunuzda ArtPuan® programına otomatik olarak katılırsınız.</p>
                </div>
                <div class="border-t border-brand-black100 pt-5">
                    <i class="ph ph-shopping-bag text-3xl text-primary"></i>
                    <h3 class="mt-4 text-lg font-medium text-brand-black100">Eser satın alın</h3>
                    <p class="mt-2 text-gray-600 leading-relaxed">Her satın aldığınız eserin toplam tutarının %1'i ArtPuan® olarak hesabınıza yüklenir.</p>
                </div>
                <div class="border-t border-brand-black100 pt-5">
                    <i class="ph ph-coins text-3xl text-primary"></i>
                    <h3 class="mt-4 text-lg font-medium text-brand-black100">Puanınızı kullanın</h3>
                    <p class="mt-2 text-gray-600 leading-relaxed">Biriken ArtPuan®'ları sonraki eser alımlarınızda 1 AP = 1 TL olarak indirim yapın.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Örnek hesaplama: metin + tinted panel --}}
    <section class="py-14 lg:py-20 bg-gray-50 border-y border-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7">
                    <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Örnek hesaplama</h2>
                    <ol class="mt-8 divide-y divide-gray-200">
                        <li class="py-5 first:pt-0">
                            <p class="font-medium text-brand-black100">100.000 TL değerinde eser satın aldınız</p>
                            <p class="text-gray-600 mt-1">Hesabınıza <span class="font-medium text-brand-black100">1.000 ArtPuan®</span> yüklenir.</p>
                        </li>
                        <li class="py-5">
                            <p class="font-medium text-brand-black100">Referans olduğunuz arkadaşınız 50.000 TL'lik eser aldı</p>
                            <p class="text-gray-600 mt-1">Hesabınıza ek <span class="font-medium text-brand-black100">500 ArtPuan®</span> yüklenir.</p>
                        </li>
                        <li class="py-5 last:pb-0">
                            <p class="font-medium text-brand-black100">Toplam 1.500 ArtPuan® biriktirdiniz</p>
                            <p class="text-gray-600 mt-1">Sonraki alışverişinizde <span class="font-medium text-brand-black100">1.500 TL indirim</span> olarak kullanabilirsiniz.</p>
                        </li>
                    </ol>
                </div>
                <div class="lg:col-span-5 bg-primary/10 p-10 text-center">
                    <p class="text-sm text-gray-600">Toplam birikiminiz</p>
                    <p class="text-5xl font-semibold tracking-tight text-brand-black100 mt-2">1.500 <span class="text-2xl font-normal text-gray-600">AP</span></p>
                    <div class="w-12 h-px bg-brand-black100/20 mx-auto my-6"></div>
                    <p class="text-sm text-gray-600">Kullanılabilir indirim</p>
                    <p class="text-3xl font-semibold tracking-tight text-brand-black100 mt-1">1.500 TL</p>
                    <p class="text-sm text-gray-600 mt-6">1 ArtPuan® = 1 TL. Puanlarınız süresiz birikir.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Avantajlar: 2x2 satır listesi --}}
    <section class="py-14 lg:py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100 mb-10">ArtPuan® avantajları</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-8">
                <div class="flex gap-5">
                    <i class="ph ph-percent text-2xl text-primary flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h3 class="text-lg font-medium text-brand-black100">%1 geri kazanım</h3>
                        <p class="mt-1 text-gray-600 leading-relaxed">Her satın aldığınız eserin toplam tutarının %1'i ArtPuan® olarak hesabınıza döner.</p>
                    </div>
                </div>
                <div class="flex gap-5">
                    <i class="ph ph-users-three text-2xl text-primary flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h3 class="text-lg font-medium text-brand-black100">Referans kazancı</h3>
                        <p class="mt-1 text-gray-600 leading-relaxed">Davet ettiğiniz arkadaşınız ayrı bir hesap açar; ilk alışverişini yaptığında hem siz hem o ekstra ArtPuan® kazanırsınız.</p>
                    </div>
                </div>
                <div class="flex gap-5">
                    <i class="ph ph-infinity text-2xl text-primary flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h3 class="text-lg font-medium text-brand-black100">Sınırsız birikim</h3>
                        <p class="mt-1 text-gray-600 leading-relaxed">ArtPuan®'larınızın bir sınırı veya son kullanma tarihi yoktur. Dilediğiniz zaman kullanın.</p>
                    </div>
                </div>
                <div class="flex gap-5">
                    <i class="ph ph-cursor-click text-2xl text-primary flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h3 class="text-lg font-medium text-brand-black100">Kolay kullanım</h3>
                        <p class="mt-1 text-gray-600 leading-relaxed">Ödeme sırasında tek tıkla ArtPuan®'larınızı kullanabilirsiniz. Ek işlem gerekmez.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Referans programı: iki ayrı hesap şeması --}}
    <section class="py-14 lg:py-20 bg-gray-50 border-y border-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-6">
                    <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Referans programı</h2>
                    <p class="mt-4 text-gray-600 leading-relaxed max-w-[55ch]">
                        Davet ettiğiniz arkadaşınız <span class="font-medium text-brand-black100">kendi adıyla ayrı bir hesap açar</span>. İlk alışverişini yaptığında hem siz hem o ekstra ArtPuan® kazanırsınız.
                    </p>
                    <p class="mt-3 text-gray-600 leading-relaxed max-w-[55ch]">
                        ArtPuan® bakiyeleri kişiye özeldir; hesaplar veya bakiyeler birleşmez. Referans kazancı, davet ettiğiniz herkesin alışverişlerinden ömür boyu devam eder.
                    </p>
                </div>
                <div class="lg:col-span-6">
                    <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                        <div class="bg-white border border-gray-200 p-6 text-center">
                            <i class="ph ph-user text-3xl text-primary"></i>
                            <p class="mt-3 font-medium text-brand-black100">Siz</p>
                            <p class="text-sm text-gray-500">Davet eden hesap</p>
                            <p class="text-sm text-brand-black100 font-medium mt-3">+%1 ArtPuan®</p>
                        </div>
                        <i class="ph ph-arrows-left-right text-2xl text-gray-400"></i>
                        <div class="bg-white border border-gray-200 p-6 text-center">
                            <i class="ph ph-user text-3xl text-gray-500"></i>
                            <p class="mt-3 font-medium text-brand-black100">Arkadaşınız</p>
                            <p class="text-sm text-gray-500">Ayrı bir hesap</p>
                            <p class="text-sm text-brand-black100 font-medium mt-3">Hoş geldin bonusu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Hesap / CTA --}}
    <section class="py-14 lg:py-20">
        <div class="container mx-auto px-4">
            @auth
            @php
                $authUser = auth()->user();
                $referralsCount = \App\Models\User::where('referred_by', $authUser->id)->count();
            @endphp
            <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">ArtPuan® hesabınız</h2>
            <p class="text-gray-600 mt-2">Merhaba <span class="font-medium text-brand-black100">{{ $authUser->name }}</span>, güncel durumunuz aşağıda.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-primary/10 p-7">
                    <p class="text-sm text-gray-600">ArtPuan® bakiyeniz</p>
                    <p class="text-4xl font-semibold tracking-tight text-brand-black100 mt-2">{{ number_format($authUser->art_puan, 0, ',', '.') }} <span class="text-lg font-normal text-gray-600">AP</span></p>
                    <p class="text-sm text-gray-600 mt-2">1 AP = 1 TL</p>
                </div>
                <div class="border border-gray-200 p-7">
                    <p class="text-sm text-gray-600">Referanslarınız</p>
                    <p class="text-4xl font-semibold tracking-tight text-brand-black100 mt-2">{{ $referralsCount }} <span class="text-lg font-normal text-gray-600">kişi</span></p>
                </div>
                <div class="border border-gray-200 p-7" x-data="{ copied: false }">
                    <p class="text-sm text-gray-600">Referans linkiniz</p>
                    <div class="flex mt-3">
                        <input type="text" value="{{ $authUser->referral_link }}" readonly aria-label="Referans linki" class="flex-1 min-w-0 bg-gray-50 border border-gray-200 border-r-0 px-3 py-2 text-sm text-gray-700 focus:outline-none truncate">
                        <button @click="navigator.clipboard.writeText('{{ $authUser->referral_link }}'); copied = true; setTimeout(() => copied = false, 2000)" class="btn-press bg-brand-black100 text-white px-4 py-2 text-sm hover:bg-primary transition">
                            <span x-show="!copied">Kopyala</span>
                            <span x-show="copied" x-cloak>Kopyalandı</span>
                        </button>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <a href="https://wa.me/?text={{ urlencode('BeArtShare\'de harika eserler keşfet! ' . $authUser->referral_link) }}" target="_blank" rel="noopener" aria-label="WhatsApp ile paylaş" class="w-9 h-9 inline-flex items-center justify-center border border-gray-300 text-brand-black100 hover:border-brand-black100 transition"><i class="ph ph-whatsapp-logo text-lg"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($authUser->referral_link) }}" target="_blank" rel="noopener" aria-label="Facebook ile paylaş" class="w-9 h-9 inline-flex items-center justify-center border border-gray-300 text-brand-black100 hover:border-brand-black100 transition"><i class="ph ph-facebook-logo text-lg"></i></a>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Kod: <span class="font-mono text-brand-black100">{{ $authUser->referral_code }}</span></p>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('profile', 'artpuan') }}" class="btn-press inline-flex items-center gap-2 bg-brand-black100 text-white px-7 py-3.5 text-sm font-medium hover:bg-primary transition-colors">
                    ArtPuan® hareketlerim <i class="ph ph-arrow-right"></i>
                </a>
                <a href="{{ route('artworks') }}" class="btn-press inline-flex items-center gap-2 border border-gray-300 text-brand-black100 px-7 py-3.5 text-sm font-medium hover:border-brand-black100 transition-colors">
                    Eserleri keşfet
                </a>
            </div>
            @else
            <div class="max-w-[60ch]">
                <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Üye olun, kazanmaya başlayın</h2>
                <p class="mt-3 text-gray-600 leading-relaxed">Hem kendi alımlarınız hem de dostlarınızın alımlarıyla biriktirdiğiniz ArtPuan®'larla sanat koleksiyonunuzu zenginleştirin.</p>
                <a href="{{ route('register') }}" class="btn-press mt-6 inline-flex items-center gap-2 bg-brand-black100 text-white px-7 py-3.5 text-sm font-medium hover:bg-primary transition-colors">
                    Üye ol <i class="ph ph-arrow-right"></i>
                </a>
                <p class="text-sm text-gray-500 mt-4">Detaylar için <a href="tel:02122906413" class="text-brand-black100 underline underline-offset-4 hover:text-primary">0212 290 64 13</a></p>
            </div>
            @endauth
        </div>
    </section>

    <section class="border-t border-gray-100">
        <div class="container mx-auto px-4 py-6">
            <p class="text-xs text-gray-500 leading-relaxed max-w-[90ch]">
                ArtPuan® oranları ve kullanım şartları ilgili eserin sayfasında belirtilir. ArtPuan®'lar nakit olarak ödenmez, yalnızca sitemizden eser alımında kullanılabilir. ArtPuan®'lar kişiye özeldir ve devredilemez. BeArtShare, program koşullarında değişiklik yapma hakkını saklı tutar.
            </p>
        </div>
    </section>
</x-layouts.app>
