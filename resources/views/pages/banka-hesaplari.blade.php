<x-layouts.app
    title="Banka Hesapları | BeArtShare - Havale / EFT Bilgileri"
    metaDescription="BeArtShare banka hesap bilgileri. Havale ve EFT ile güvenli ödeme yapın."
    metaKeywords="beartshare banka hesapları, havale, eft, iban, ödeme bilgileri"
>
    <!-- Page Header -->
    <section class="bg-brand-black100 py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-10 right-10 w-40 h-40 border border-white rounded-full"></div>
            <div class="absolute bottom-10 left-20 w-60 h-60 border border-white rounded-full"></div>
        </div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-3xl">
                <span class="inline-block bg-amber-500/20 text-amber-400 text-xs font-semibold px-3 py-1 rounded-full mb-4 tracking-wider uppercase">Ödeme Bilgileri</span>
                <h1 class="text-3xl md:text-4xl font-light text-white">Banka <span class="font-semibold">Hesaplarımız</span></h1>
                <p class="text-white/50 text-sm mt-3 max-w-xl leading-relaxed">Havale / EFT ile güvenli ödeme için banka hesap bilgilerimiz aşağıdadır.</p>
            </div>
        </div>
    </section>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto">

            <!-- Firma Bilgisi -->
            <div class="text-center mb-10">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Hesap Sahibi</p>
                <h2 class="text-lg font-semibold text-brand-black100">BEARTSHARE ONLİNE SANAT GALERİSİ ANONİM ŞİRKETİ</h2>
            </div>

            <div class="space-y-6">

                <!-- Garanti Bankası -->
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-lg">Garanti Bankası</h3>
                            <p class="text-green-100 text-xs">Galatasaray Şubesi</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <span class="text-xs text-gray-400 uppercase tracking-wider">Hesap No</span>
                            <span class="text-sm font-mono font-semibold text-brand-black100">068-6291752</span>
                        </div>
                        <div class="border-t border-gray-50"></div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <span class="text-xs text-gray-400 uppercase tracking-wider">IBAN</span>
                            <div class="flex items-center gap-2">
                                <span id="iban-garanti" class="text-sm font-mono font-semibold text-brand-black100 tracking-wide">TR62 0006 2000 0680 0006 2917 52</span>
                                <button onclick="copyIban('iban-garanti')" class="text-gray-300 hover:text-green-600 transition" title="IBAN Kopyala">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vakıflar Bankası -->
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-700 to-blue-800 px-6 py-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-lg">Vakıflar Bankası</h3>
                            <p class="text-blue-100 text-xs">Levent Şubesi</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <span class="text-xs text-gray-400 uppercase tracking-wider">IBAN</span>
                            <div class="flex items-center gap-2">
                                <span id="iban-vakif" class="text-sm font-mono font-semibold text-brand-black100 tracking-wide">TR68 0001 5001 5800 7321 4175 83</span>
                                <button onclick="copyIban('iban-vakif')" class="text-gray-300 hover:text-blue-600 transition" title="IBAN Kopyala">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Uyarı / Bilgilendirme -->
            <div class="mt-10 space-y-4">
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-amber-800 mb-1">Havale / EFT Yaparken Dikkat!</h4>
                            <ul class="text-amber-700 text-sm space-y-1.5">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-400 mt-1">•</span>
                                    <span>Açıklama alanına mutlaka <strong>sipariş kodunuzu</strong> (örn: BA-XXXXXX) yazınız.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-400 mt-1">•</span>
                                    <span>Sipariş kodunu yazmamanız durumunda ödemeniz eşleştirilemeyebilir.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-400 mt-1">•</span>
                                    <span>Eserler sipariş oluşturulduktan sonra <strong>30 dakika</strong> süreyle sizin için rezerve edilir.</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-400 mt-1">•</span>
                                    <span>Ödeme dekontu için: <a href="mailto:info@beartshare.com" class="underline font-medium hover:text-amber-900 transition">info@beartshare.com</a></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-100 rounded-xl p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-1">Destek</h4>
                            <p class="text-gray-500 text-sm">Ödeme ile ilgili herhangi bir sorunuz varsa <a href="{{ route('contact') }}" class="underline font-medium text-gray-700 hover:text-brand-black100 transition">İletişim</a> sayfamızdan bize ulaşabilirsiniz.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Butonlar -->
            <div class="mt-10 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('artworks') }}" class="bg-brand-black100 hover:bg-black text-white py-3 px-8 text-sm font-medium transition text-center rounded-lg">
                    Eserleri Keşfet
                </a>
                <a href="{{ route('home') }}" class="border border-gray-200 py-3 px-8 text-sm text-gray-500 hover:bg-gray-50 transition text-center rounded-lg">
                    Ana Sayfa
                </a>
            </div>

        </div>
    </div>

    <script>
        function copyIban(elementId) {
            const ibanText = document.getElementById(elementId).innerText.replace(/\s/g, '');
            navigator.clipboard.writeText(ibanText).then(() => {
                const btn = document.getElementById(elementId).nextElementSibling;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                setTimeout(() => { btn.innerHTML = originalHtml; }, 2000);
            });
        }
    </script>
</x-layouts.app>
