<x-layouts.app title="ArtPuan - BeArtShare">
    <!-- Page Header -->
    <section class="bg-brand-black100 py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-10 left-10 w-40 h-40 border border-white rounded-full"></div>
            <div class="absolute bottom-10 right-20 w-60 h-60 border border-white rounded-full"></div>
            <div class="absolute top-1/2 left-1/3 w-20 h-20 border border-white rounded-full"></div>
        </div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-3xl">
                <span class="inline-block bg-primary/20 text-primary text-xs font-semibold px-3 py-1 rounded-full mb-4 tracking-wider uppercase">Sadakat Programı</span>
                <h1 class="text-3xl md:text-5xl font-light text-white leading-tight">Art<span class="font-bold text-primary">Puan</span></h1>
                <p class="text-white/50 text-sm mt-3 max-w-xl leading-relaxed">Sanat galericiliğinde bir ilk... Her alışverişinizde puan kazanın, sanat koleksiyonunuzu büyütün.</p>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-14">
                <h2 class="text-2xl font-light text-brand-black100">Nasıl <span class="font-semibold">Çalışır?</span></h2>
                <p class="text-gray-400 text-sm mt-2">Üç kolay adımda ArtPuan kazanmaya başlayın</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Step 1 -->
                <div class="relative text-center group">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-50 border-2 border-gray-100 flex items-center justify-center group-hover:border-primary group-hover:bg-primary/5 transition-all duration-300">
                        <svg class="w-8 h-8 text-brand-black100 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-2 bg-primary text-white text-[10px] font-bold w-6 h-6 rounded-full flex items-center justify-center">1</span>
                    <h3 class="text-base font-semibold text-brand-black100 mb-2">Üye Olun</h3>
                    <p class="text-gray-400 text-xs leading-relaxed px-4">BeArtShare'e ücretsiz üye olarak ArtPuan programına otomatik olarak katılın.</p>
                    <!-- Arrow -->
                    <div class="hidden md:block absolute top-10 -right-4 w-8">
                        <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative text-center group">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-50 border-2 border-gray-100 flex items-center justify-center group-hover:border-primary group-hover:bg-primary/5 transition-all duration-300">
                        <svg class="w-8 h-8 text-brand-black100 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <span class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-2 bg-primary text-white text-[10px] font-bold w-6 h-6 rounded-full flex items-center justify-center">2</span>
                    <h3 class="text-base font-semibold text-brand-black100 mb-2">Eser Satın Alın</h3>
                    <p class="text-gray-400 text-xs leading-relaxed px-4">Her satın aldığınız eser için toplam tutarın %1'i kadar ArtPuan hesabınıza yüklenir.</p>
                    <!-- Arrow -->
                    <div class="hidden md:block absolute top-10 -right-4 w-8">
                        <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative text-center group">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-50 border-2 border-gray-100 flex items-center justify-center group-hover:border-primary group-hover:bg-primary/5 transition-all duration-300">
                        <svg class="w-8 h-8 text-brand-black100 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-2 bg-primary text-white text-[10px] font-bold w-6 h-6 rounded-full flex items-center justify-center">3</span>
                    <h3 class="text-base font-semibold text-brand-black100 mb-2">Puanınızı Kullanın</h3>
                    <p class="text-gray-400 text-xs leading-relaxed px-4">Biriktirdiğiniz ArtPuan'ları sonraki eser alımlarınızda indirim olarak kullanın.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Benefits -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-14">
                <h2 class="text-2xl font-light text-brand-black100">ArtPuan <span class="font-semibold">Avantajları</span></h2>
                <p class="text-gray-400 text-sm mt-2">Sanat koleksiyonunuzu büyütmenin en akıllı yolu</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                <!-- Benefit 1 -->
                <div class="bg-white border border-gray-100 p-7 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-5 group-hover:bg-primary/20 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-brand-black100 mb-2">%1 Geri Kazanım</h3>
                    <p class="text-gray-400 text-xs leading-relaxed">Her satın aldığınız eserin toplam tutarının %1'i ArtPuan olarak hesabınıza yüklenir.</p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white border border-gray-100 p-7 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-5 group-hover:bg-primary/20 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-brand-black100 mb-2">Referans Kazancı</h3>
                    <p class="text-gray-400 text-xs leading-relaxed">Dostlarınızı referans gösterin, onların tüm alışverişlerinden ömür boyu ArtPuan kazanın.</p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white border border-gray-100 p-7 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-5 group-hover:bg-primary/20 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-brand-black100 mb-2">Sınırsız Birikim</h3>
                    <p class="text-gray-400 text-xs leading-relaxed">ArtPuan'larınızın bir sınırı veya son kullanma tarihi yoktur. Dilediğiniz zaman kullanın.</p>
                </div>

                <!-- Benefit 4 -->
                <div class="bg-white border border-gray-100 p-7 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-5 group-hover:bg-primary/20 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-brand-black100 mb-2">Kolay Kullanım</h3>
                    <p class="text-gray-400 text-xs leading-relaxed">Ödeme sırasında tek tıkla ArtPuan'larınızı kullanabilirsiniz. Herhangi bir ek işlem gerekmez.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Example Calculation -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-2xl font-light text-brand-black100 mb-2">Örnek <span class="font-semibold">Hesaplama</span></h2>
                        <p class="text-gray-400 text-sm mb-8">ArtPuan'larınız nasıl birikir, bir örnekle görelim.</p>

                        <div class="space-y-5">
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-brand-black100 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-semibold">1</div>
                                <div>
                                    <p class="text-sm text-brand-black100 font-medium">100.000 TL değerinde eser satın aldınız</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Hesabınıza <span class="text-primary font-semibold">1.000 ArtPuan</span> yüklenir</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-brand-black100 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-semibold">2</div>
                                <div>
                                    <p class="text-sm text-brand-black100 font-medium">Referans olduğunuz arkadaşınız 50.000 TL'lik eser aldı</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Hesabınıza ek <span class="text-primary font-semibold">500 ArtPuan</span> yüklenir</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-primary text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-semibold">3</div>
                                <div>
                                    <p class="text-sm text-brand-black100 font-medium">Toplam 1.500 ArtPuan biriktirdiniz</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Sonraki alışverişinizde <span class="text-primary font-semibold">1.500 TL indirim</span> olarak kullanabilirsiniz</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-brand-black100 p-8 md:p-10 text-white">
                        <div class="text-center">
                            <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Toplam Birikiminiz</p>
                            <p class="text-5xl font-bold text-primary mb-2">1.500</p>
                            <p class="text-white/60 text-sm">ArtPuan</p>
                            <div class="w-16 h-px bg-white/10 mx-auto my-6"></div>
                            <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Kullanılabilir İndirim</p>
                            <p class="text-3xl font-semibold text-white">1.500 <span class="text-lg font-normal text-white/60">TL</span></p>
                            <div class="w-16 h-px bg-white/10 mx-auto my-6"></div>
                            <p class="text-white/30 text-[10px] leading-relaxed">1 ArtPuan = 1 TL değerindedir.<br>Puanlarınız hesabınızda süresiz olarak birikir.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Referral Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-light text-brand-black100 mb-3">Referans <span class="font-semibold">Programı</span></h2>
                <p class="text-gray-400 text-sm max-w-2xl mx-auto mb-10 leading-relaxed">
                    BeArtShare'e üye olduktan sonra dilediğiniz sayıda dostunuza referans olabilirsiniz.
                    Referans olduğunuz kişinin tüm alımlarından <span class="text-brand-black100 font-medium">ömür boyu ArtPuan kazanmaya devam edersiniz</span>.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white border border-gray-100 p-6">
                        <p class="text-3xl font-bold text-primary mb-1">&#8734;</p>
                        <p class="text-xs text-gray-400">Sınırsız referans hakkı</p>
                    </div>
                    <div class="bg-white border border-gray-100 p-6">
                        <p class="text-3xl font-bold text-primary mb-1">%1</p>
                        <p class="text-xs text-gray-400">Referans kazanım oranı</p>
                    </div>
                    <div class="bg-white border border-gray-100 p-6">
                        <p class="text-3xl font-bold text-primary mb-1">&#8734;</p>
                        <p class="text-xs text-gray-400">Ömür boyu kazanım</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl font-light text-brand-black100 mb-3">Hemen Üye Olun, <span class="font-semibold text-primary">Kazanmaya Başlayın</span></h2>
                <p class="text-gray-400 text-sm mb-8 max-w-xl mx-auto leading-relaxed">
                    Hem kendi alımlarınız hem de dostlarınızın alımları ile biriktirdiğiniz ArtPuan'larla sanat koleksiyonunuzu zenginleştirin.
                </p>
                <a href="{{ route('register') }}" class="inline-flex items-center bg-brand-black100 text-white px-10 py-4 text-sm font-medium hover:bg-primary transition-all duration-300 gap-2">
                    Üye Ol ve ArtPuan Kazanmaya Başla
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <p class="text-gray-300 text-[10px] mt-6">
                    Detaylar için <a href="tel:02122906413" class="text-primary hover:underline">0212 290 64 13</a>'ü arayabilirsiniz.
                </p>
            </div>
        </div>
    </section>

    <!-- Terms Note -->
    <section class="border-t border-gray-100">
        <div class="container mx-auto px-4 py-6">
            <p class="text-gray-300 text-[10px] leading-relaxed max-w-4xl mx-auto text-center">
                * ArtPuan oranları ve kullanım şartları ilgili eserin sayfasında belirtilecektir. ArtPuan'lar nakdi olarak ödenmeyecek olup, sadece sitemizden eser alımında kullanılabilecektir. ArtPuan'lar şahsa özel olup başkalarına devredilemeyecektir. BeArtShare, ArtPuan programı koşullarında değişiklik yapma hakkını saklı tutar.
            </p>
        </div>
    </section>
</x-layouts.app>
