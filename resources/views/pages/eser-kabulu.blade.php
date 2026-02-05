<x-layouts.app
    title="Eser Kabulu | BeArtShare"
    metaDescription="BeArtShare'e eserlerinizi gonderin. Dusuk komisyon oranlariyla eserlerinizi guvenle satisa cikarin."
    metaRobots="index, follow"
>
    <!-- Hero -->
    <section class="relative overflow-hidden" style="background: linear-gradient(160deg, #5C4290 0%, #7052A8 40%, #8664BE 70%, #5C4290 100%);">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <span style="font-family: Prompt, sans-serif; font-weight: 900; font-size: clamp(10rem, 20vw, 18rem); line-height: 0.82; letter-spacing: -0.03em; color: rgba(80,55,140,0.12); position: absolute; right: -5%; top: -10%; user-select: none;">ART</span>
        </div>
        <div class="container mx-auto px-4 py-16 md:py-20 relative z-10">
            <div class="max-w-2xl">
                <span class="inline-block text-white/50 text-xs font-medium tracking-[0.25em] uppercase mb-4">Sanatcilar Icin</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-[0.95] tracking-tight">
                    Eser Kabulu
                </h1>
                <p class="text-white/75 text-sm md:text-base leading-relaxed mt-5 max-w-lg">
                    Eserlerinizi BeArtShare araciligiyla, cok dusuk komisyon oranlariyla seffaf ve guvenli bir sekilde satisa cikarin.
                </p>
            </div>
        </div>
    </section>

    <!-- Info Cards -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="bg-white rounded-xl p-6 border border-gray-100 text-center">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-purple-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-brand-black100 text-sm mb-2">Profesyonel Fotograf</h3>
                    <p class="text-gray-400 text-xs leading-relaxed">Eserleriniz profesyonel ekibimiz tarafindan fotograflanir ve kataloglanir.</p>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-100 text-center">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-green-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-brand-black100 text-sm mb-2">Dusuk Komisyon</h3>
                    <p class="text-gray-400 text-xs leading-relaxed">Sektordeki en uygun komisyon oranlariyla eserlerinizi satisa sunun.</p>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-100 text-center">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-semibold text-brand-black100 text-sm mb-2">Guvenli Satis</h3>
                    <p class="text-gray-400 text-xs leading-relaxed">Tum islemler guvenli odeme altyapisi uzerinden gerceklestirilir.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <div class="text-center mb-10">
                    <h2 class="text-2xl font-light text-brand-black100">Eser <span class="font-semibold">Basvuru Formu</span></h2>
                    <p class="text-gray-400 text-sm mt-2">Eserleriniz hakkinda bilgi gonderin, ekibimiz sizinle iletisime gececektir.</p>
                </div>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center mb-8">
                        <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="text-lg font-semibold text-green-800 mb-1">Basvurunuz Alindi!</h3>
                        <p class="text-green-600 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('eser-kabulu.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Kisisel Bilgiler -->
                    <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-brand-black100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Kisisel Bilgiler
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Ad Soyad <span class="text-red-400">*</span></label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition bg-white @error('name') border-red-300 @enderror">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Telefon <span class="text-red-400">*</span></label>
                                <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="05XX XXX XX XX" required
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition bg-white @error('phone') border-red-300 @enderror">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">E-posta <span class="text-red-400">*</span></label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required
                                   class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition bg-white @error('email') border-red-300 @enderror">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Eser Bilgileri -->
                    <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-brand-black100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Eser Bilgileri
                        </h3>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Sanatci Adi <span class="text-red-400">*</span></label>
                            <input type="text" name="artist_name" value="{{ old('artist_name') }}" placeholder="Eserin sanatcisinin adi" required
                                   class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition bg-white @error('artist_name') border-red-300 @enderror">
                            @error('artist_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Eser Adi <span class="text-red-400">*</span></label>
                                <input type="text" name="artwork_title" value="{{ old('artwork_title') }}" required
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition bg-white @error('artwork_title') border-red-300 @enderror">
                                @error('artwork_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Teknik</label>
                                <select name="technique" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition bg-white">
                                    <option value="">Seciniz</option>
                                    <option value="Yagli Boya" {{ old('technique') === 'Yagli Boya' ? 'selected' : '' }}>Yagli Boya</option>
                                    <option value="Akrilik" {{ old('technique') === 'Akrilik' ? 'selected' : '' }}>Akrilik</option>
                                    <option value="Suluboya" {{ old('technique') === 'Suluboya' ? 'selected' : '' }}>Suluboya</option>
                                    <option value="Karisik Teknik" {{ old('technique') === 'Karisik Teknik' ? 'selected' : '' }}>Karisik Teknik</option>
                                    <option value="Heykel" {{ old('technique') === 'Heykel' ? 'selected' : '' }}>Heykel</option>
                                    <option value="Seramik" {{ old('technique') === 'Seramik' ? 'selected' : '' }}>Seramik</option>
                                    <option value="Baski" {{ old('technique') === 'Baski' ? 'selected' : '' }}>Baski / Litografi</option>
                                    <option value="Fotograf" {{ old('technique') === 'Fotograf' ? 'selected' : '' }}>Fotograf</option>
                                    <option value="Diger" {{ old('technique') === 'Diger' ? 'selected' : '' }}>Diger</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Boyutlar (cm)</label>
                                <input type="text" name="dimensions" value="{{ old('dimensions') }}" placeholder="orn: 100x80"
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Yapim Yili</label>
                                <input type="text" name="year" value="{{ old('year') }}" placeholder="orn: 2024"
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Beklenen Fiyat (TL)</label>
                                <input type="text" name="expected_price" value="{{ old('expected_price') }}" placeholder="orn: 50.000"
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- Eser Fotograflari -->
                    <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-brand-black100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Eser Fotograflari
                        </h3>
                        <div>
                            <label class="block text-xs text-gray-500 mb-2">Eser fotograflarini yukleyin (max 5 adet, max 5MB/adet)</label>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-primary/40 transition-colors bg-white">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <input type="file" name="images[]" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                                <p class="text-[10px] text-gray-400 mt-2">JPG, PNG veya WEBP formatlari desteklenir</p>
                            </div>
                            @error('images') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            @error('images.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Ek Bilgiler -->
                    <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-brand-black100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            Ek Bilgiler
                        </h3>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Eser Hakkinda Notlar</label>
                            <textarea name="notes" rows="4" placeholder="Eserin hikayesi, provenance (gecmis sahipleri), sergi gecmisi, sertifika bilgisi vb."
                                      class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition bg-white resize-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="text-center pt-2">
                        <button type="submit" class="inline-flex items-center bg-brand-black100 text-white px-10 py-3.5 rounded-full text-sm font-semibold hover:shadow-xl hover:scale-[1.02] transition-all duration-300 shadow-lg group">
                            Basvuru Gonder
                            <svg class="w-4 h-4 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <p class="text-[10px] text-gray-400 mt-3">Basvurunuz degerlendirilecek ve en kisa surede sizinle iletisime gecilegektir.</p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
