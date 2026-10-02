<x-layouts.app
    title="Eser Kabulü | BeArtShare"
    metaDescription="BeArtShare'e eserlerinizi gönderin. Düşük komisyon oranlarıyla eserlerinizi güvenle satışa çıkarın."
    metaRobots="index, follow"
>
    <section class="border-b border-gray-100">
        <div class="container mx-auto px-4 pt-12 pb-12 md:pt-16 md:pb-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <div class="lg:col-span-7">
                    <h1 class="text-4xl md:text-5xl font-semibold tracking-tight leading-[1.05] text-brand-black100">Koleksiyonerden koleksiyonere</h1>
                    <p class="mt-5 text-lg text-gray-600 leading-relaxed max-w-[52ch]">Elinizdeki eserleri BeArtShare aracılığıyla, düşük komisyonla, şeffaf ve güvenli bir şekilde yeni koleksiyonerlere ulaştırın.</p>
                    <a href="#basvuru" class="btn-press mt-8 inline-flex items-center gap-2 bg-brand-black100 text-white px-7 py-3.5 text-sm font-medium hover:bg-primary transition-colors">
                        Başvuru formuna git <i class="ph ph-arrow-down"></i>
                    </a>
                </div>
                <ul class="lg:col-span-5 divide-y divide-gray-100 lg:border-l lg:border-gray-100 lg:pl-10">
                    <li class="flex gap-4 py-5 first:pt-0">
                        <i class="ph ph-camera text-2xl text-primary flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h2 class="font-medium text-brand-black100">Profesyonel fotoğraf ve katalog</h2>
                            <p class="mt-1 text-sm text-gray-600 leading-relaxed">Eserleriniz ekibimiz tarafından fotoğraflanır ve kataloglanır.</p>
                        </div>
                    </li>
                    <li class="flex gap-4 py-5">
                        <i class="ph ph-percent text-2xl text-primary flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h2 class="font-medium text-brand-black100">Düşük komisyon</h2>
                            <p class="mt-1 text-sm text-gray-600 leading-relaxed">Sektördeki en uygun oranlarla satışa sunulur.</p>
                        </div>
                    </li>
                    <li class="flex gap-4 py-5 last:pb-0">
                        <i class="ph ph-shield-check text-2xl text-primary flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h2 class="font-medium text-brand-black100">Güvenli satış</h2>
                            <p class="mt-1 text-sm text-gray-600 leading-relaxed">Tüm işlemler banka ödeme altyapısı üzerinden gerçekleşir.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <section id="basvuru" class="py-14 lg:py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl">
                <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-brand-black100">Eser başvuru formu</h2>
                <p class="text-gray-600 mt-2">Eserleriniz hakkında bilgi gönderin, ekibimiz sizinle iletişime geçsin.</p>

                @if(session('success'))
                    <div class="border border-gray-200 bg-gray-50 p-6 mt-8 flex gap-4">
                        <i class="ph ph-check-circle text-2xl text-primary flex-shrink-0"></i>
                        <div>
                            <h3 class="font-medium text-brand-black100">Başvurunuz alındı</h3>
                            <p class="text-gray-600 text-sm mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('eser-kabulu.submit') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-10">
                    @csrf

                    <fieldset class="space-y-5">
                        <legend class="text-base font-medium text-brand-black100 mb-1">Kişisel bilgiler</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="block text-sm text-gray-700 mb-1.5">Ad Soyad <span class="text-primary">*</span></label>
                                <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required
                                       class="w-full border px-4 py-2.5 text-sm text-brand-black100 focus:outline-none focus:border-brand-black100 transition @error('name') border-red-500 @else border-gray-300 @enderror">
                                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-sm text-gray-700 mb-1.5">Telefon <span class="text-primary">*</span></label>
                                <input id="phone" type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="05XX XXX XX XX" required
                                       class="w-full border px-4 py-2.5 text-sm text-brand-black100 placeholder:text-gray-400 focus:outline-none focus:border-brand-black100 transition @error('phone') border-red-500 @else border-gray-300 @enderror">
                                @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm text-gray-700 mb-1.5">E-posta <span class="text-primary">*</span></label>
                            <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required
                                   class="w-full border px-4 py-2.5 text-sm text-brand-black100 focus:outline-none focus:border-brand-black100 transition @error('email') border-red-500 @else border-gray-300 @enderror">
                            @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </fieldset>

                    <fieldset class="space-y-5">
                        <legend class="text-base font-medium text-brand-black100 mb-1">Eser bilgileri</legend>
                        <div>
                            <label for="artist_name" class="block text-sm text-gray-700 mb-1.5">Sanatçı adı <span class="text-primary">*</span></label>
                            <input id="artist_name" type="text" name="artist_name" value="{{ old('artist_name') }}" required
                                   class="w-full border px-4 py-2.5 text-sm text-brand-black100 focus:outline-none focus:border-brand-black100 transition @error('artist_name') border-red-500 @else border-gray-300 @enderror">
                            @error('artist_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="artwork_title" class="block text-sm text-gray-700 mb-1.5">Eser adı <span class="text-primary">*</span></label>
                                <input id="artwork_title" type="text" name="artwork_title" value="{{ old('artwork_title') }}" required
                                       class="w-full border px-4 py-2.5 text-sm text-brand-black100 focus:outline-none focus:border-brand-black100 transition @error('artwork_title') border-red-500 @else border-gray-300 @enderror">
                                @error('artwork_title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="technique" class="block text-sm text-gray-700 mb-1.5">Teknik</label>
                                <select id="technique" name="technique" class="w-full border border-gray-300 px-4 py-2.5 text-sm text-brand-black100 bg-white focus:outline-none focus:border-brand-black100 transition">
                                    <option value="">Seçiniz</option>
                                    @foreach(['Yağlı Boya','Akrilik','Akrilik Panel','Suluboya','Karışık Teknik','Heykel','Bronz Heykel','Seramik','Baskı / Litografi','Serigrafi','Fotoğraf','Dijital Sanat','Diğer'] as $t)
                                        <option value="{{ $t }}" @selected(old('technique') === $t)>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label for="dimensions" class="block text-sm text-gray-700 mb-1.5">Boyutlar (cm)</label>
                                <input id="dimensions" type="text" name="dimensions" value="{{ old('dimensions') }}" placeholder="100x80"
                                       class="w-full border border-gray-300 px-4 py-2.5 text-sm text-brand-black100 placeholder:text-gray-400 focus:outline-none focus:border-brand-black100 transition">
                            </div>
                            <div>
                                <label for="year" class="block text-sm text-gray-700 mb-1.5">Yapım yılı</label>
                                <input id="year" type="text" name="year" value="{{ old('year') }}" placeholder="2024"
                                       class="w-full border border-gray-300 px-4 py-2.5 text-sm text-brand-black100 placeholder:text-gray-400 focus:outline-none focus:border-brand-black100 transition">
                            </div>
                            <div>
                                <label for="expected_price" class="block text-sm text-gray-700 mb-1.5">Beklenen fiyat (TL)</label>
                                <input id="expected_price" type="text" name="expected_price" value="{{ old('expected_price') }}" placeholder="50.000"
                                       class="w-full border border-gray-300 px-4 py-2.5 text-sm text-brand-black100 placeholder:text-gray-400 focus:outline-none focus:border-brand-black100 transition">
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="text-base font-medium text-brand-black100 mb-1">Eser fotoğrafları</legend>
                        <label for="images" class="block text-sm text-gray-700 mb-1.5">En fazla 5 adet, her biri en fazla 5 MB</label>
                        <div class="border border-dashed border-gray-300 p-6 hover:border-brand-black100 transition">
                            <input id="images" type="file" name="images[]" multiple accept="image/*" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-brand-black100 file:text-white hover:file:bg-primary cursor-pointer">
                            <p class="text-sm text-gray-500 mt-3">JPG, PNG veya WEBP</p>
                        </div>
                        @error('images') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        @error('images.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </fieldset>

                    <fieldset>
                        <legend class="text-base font-medium text-brand-black100 mb-1">Ek bilgiler</legend>
                        <label for="notes" class="block text-sm text-gray-700 mb-1.5">Eser hakkında notlar</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Eserin hikâyesi, geçmiş sahipleri, sergi geçmişi, sertifika bilgisi"
                                  class="w-full border border-gray-300 px-4 py-2.5 text-sm text-brand-black100 placeholder:text-gray-400 focus:outline-none focus:border-brand-black100 transition resize-none">{{ old('notes') }}</textarea>
                    </fieldset>

                    <div>
                        <button type="submit" class="btn-press inline-flex items-center gap-2 bg-brand-black100 text-white px-8 py-3.5 text-sm font-medium hover:bg-primary transition-colors">
                            Başvuruyu gönder <i class="ph ph-arrow-right"></i>
                        </button>
                        <p class="text-sm text-gray-500 mt-3">Başvurunuz değerlendirilir ve en kısa sürede sizinle iletişime geçilir.</p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
