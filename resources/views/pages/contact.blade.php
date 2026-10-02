<x-layouts.app
    title="İletişim | BeArtShare - Bize Ulaşın"
    metaDescription="BeArtShare ile iletişime geçin. Harmancı Giz Plaza, Esentepe/İstanbul. Telefon: 0510 221 64 13, E-posta: info@beartshare.com"
    metaKeywords="beartshare iletişim, sanat galerisi iletişim, beartshare telefon, beartshare adres, esentepe sanat galerisi"
>
    <x-page-header title="İletişim" subtitle="Sorularınız ve önerileriniz için bize ulaşın" />

    <div class="container mx-auto px-4 py-12 lg:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

            {{-- İletişim bilgileri --}}
            <aside class="lg:col-span-4">
                <p class="font-medium text-brand-black100">Beartshare Online Sanat Galerisi A.Ş.</p>

                <dl class="mt-6 divide-y divide-gray-100">
                    <div class="flex gap-4 py-4 first:pt-0">
                        <dt class="sr-only">Adres</dt>
                        <i class="ph ph-map-pin text-xl text-primary flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                        <dd class="text-gray-600 leading-relaxed">Harmancı Giz Plaza<br>Harman Sokak No: 5 K: 21 D: 118<br>Esentepe / İstanbul</dd>
                    </div>
                    <div class="flex gap-4 py-4">
                        <dt class="sr-only">Telefon</dt>
                        <i class="ph ph-phone text-xl text-primary flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                        <dd class="text-gray-600 leading-relaxed">
                            <a href="tel:05102216413" class="hover:text-brand-black100 transition">0510 221 64 13</a><br>
                            <a href="tel:02122906413" class="hover:text-brand-black100 transition">0212 290 64 13</a>
                        </dd>
                    </div>
                    <div class="flex gap-4 py-4">
                        <dt class="sr-only">E-posta</dt>
                        <i class="ph ph-envelope-simple text-xl text-primary flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                        <dd class="text-gray-600"><a href="mailto:info@beartshare.com" class="hover:text-brand-black100 transition">info@beartshare.com</a></dd>
                    </div>
                    <div class="flex gap-4 py-4">
                        <dt class="sr-only">Çalışma saatleri</dt>
                        <i class="ph ph-clock text-xl text-primary flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                        <dd class="text-gray-600 leading-relaxed">Pazartesi - Cuma: 09:00 - 18:00<br>Cumartesi: 10:00 - 15:00<br>Pazar: Kapalı</dd>
                    </div>
                </dl>

                <div class="mt-6 flex gap-3">
                    <a href="https://wa.me/905102216413" target="_blank" rel="noopener" class="btn-press inline-flex items-center gap-2 border border-gray-300 px-4 py-2.5 text-sm text-brand-black100 hover:border-brand-black100 transition">
                        <i class="ph ph-whatsapp-logo text-lg"></i> WhatsApp
                    </a>
                    <a href="https://www.instagram.com/beartshare" target="_blank" rel="noopener" class="btn-press inline-flex items-center gap-2 border border-gray-300 px-4 py-2.5 text-sm text-brand-black100 hover:border-brand-black100 transition">
                        <i class="ph ph-instagram-logo text-lg"></i> Instagram
                    </a>
                </div>

                <a href="{{ route('eser-kabulu') }}" class="group mt-10 block border-t border-gray-100 pt-6">
                    <p class="font-medium text-brand-black100">Eserinizi satmak mı istiyorsunuz?</p>
                    <p class="text-sm text-gray-600 mt-1">Eser kabul formunu doldurun, ekibimiz sizinle iletişime geçsin.</p>
                    <span class="mt-2 inline-flex items-center gap-1 text-sm text-brand-black100 underline underline-offset-4 group-hover:text-primary transition">Eser Kabulü <i class="ph ph-arrow-right"></i></span>
                </a>
            </aside>

            {{-- Form --}}
            <div class="lg:col-span-8">
                <h2 class="text-2xl font-semibold tracking-tight text-brand-black100">Bize yazın</h2>
                <p class="text-gray-600 mt-2 max-w-[60ch]">Sorularınız, önerileriniz veya iş birliği teklifleriniz için formu doldurun, en kısa sürede dönüş yapalım.</p>

                @if(session('success'))
                    <div class="border border-gray-200 bg-gray-50 p-5 mt-6 flex gap-3">
                        <i class="ph ph-check-circle text-2xl text-primary flex-shrink-0"></i>
                        <p class="text-gray-700">{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="border border-red-200 bg-red-50 p-5 mt-6 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" class="mt-8 space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="c-name" class="block text-sm text-gray-700 mb-1.5">Adınız Soyadınız <span class="text-primary">*</span></label>
                            <input id="c-name" type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 px-4 py-3 text-sm text-brand-black100 focus:outline-none focus:border-brand-black100 transition">
                        </div>
                        <div>
                            <label for="c-email" class="block text-sm text-gray-700 mb-1.5">E-posta adresiniz <span class="text-primary">*</span></label>
                            <input id="c-email" type="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 px-4 py-3 text-sm text-brand-black100 focus:outline-none focus:border-brand-black100 transition">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="c-phone" class="block text-sm text-gray-700 mb-1.5">Telefon numaranız</label>
                            <input id="c-phone" type="tel" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 px-4 py-3 text-sm text-brand-black100 focus:outline-none focus:border-brand-black100 transition">
                        </div>
                        <div>
                            <label for="c-subject" class="block text-sm text-gray-700 mb-1.5">Konu <span class="text-primary">*</span></label>
                            <select id="c-subject" name="subject" required class="w-full border border-gray-300 px-4 py-3 text-sm text-brand-black100 bg-white focus:outline-none focus:border-brand-black100 transition">
                                <option value="">Konu seçin</option>
                                @foreach(\App\Models\ContactMessage::SUBJECTS as $key => $label)
                                    <option value="{{ $key }}" @selected(old('subject') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="c-message" class="block text-sm text-gray-700 mb-1.5">Mesajınız <span class="text-primary">*</span></label>
                        <textarea id="c-message" name="message" rows="6" required class="w-full border border-gray-300 px-4 py-3 text-sm text-brand-black100 focus:outline-none focus:border-brand-black100 transition resize-none">{{ old('message') }}</textarea>
                    </div>
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="kvkk" name="kvkk" value="1" required class="mt-1 w-4 h-4 border-gray-300 text-primary focus:ring-primary">
                        <label for="kvkk" class="text-sm text-gray-600">
                            <a href="{{ route('gizlilik-kvkk') }}" class="text-brand-black100 underline underline-offset-4 hover:text-primary transition">KVKK Aydınlatma Metni</a>'ni okudum ve kabul ediyorum.
                        </label>
                    </div>
                    <button type="submit" class="btn-press inline-flex items-center gap-2 bg-brand-black100 text-white px-8 py-3.5 text-sm font-medium hover:bg-primary transition-colors">
                        Mesajı gönder <i class="ph ph-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <section class="bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-4 py-12">
            <div class="aspect-[21/7] bg-gray-200 overflow-hidden">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3008.7!2d29.024!3d41.066!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab7a0c7a38e61%3A0x91d45a2b4d3ad0a0!2sHarmanc%C4%B1%20Giz%20Plaza!5e0!3m2!1str!2str!4v1700000000000!5m2!1str!2str"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full" title="BeArtShare konum haritası"
                ></iframe>
            </div>
        </div>
    </section>
</x-layouts.app>
