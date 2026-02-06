<div>
    <!-- Hero -->
    <div class="bg-gray-50 border-b border-gray-100">
        <div class="container mx-auto px-4 py-12 text-center">
            <h1 class="text-3xl md:text-4xl font-light text-brand-black100 mb-3">
                Sikca Sorulan <span class="font-semibold">Sorular</span>
            </h1>
            <p class="text-gray-400 text-sm max-w-xl mx-auto">
                BeArtShare hakkinda merak ettiginiz her seyin cevabi burada. Aradiginizi bulamadiysaniz bizimle iletisime gecebilirsiniz.
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Kategori Filtreleri -->
            @if($categories->count() > 0)
                <div class="flex flex-wrap justify-center gap-2 mb-10">
                    <button wire:click="setCategory('')"
                            class="px-4 py-2 text-sm rounded-full transition
                                {{ $selectedCategory === '' ? 'bg-brand-black100 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Tumu
                    </button>
                    @foreach($categories as $key => $label)
                        <button wire:click="setCategory('{{ $key }}')"
                                class="px-4 py-2 text-sm rounded-full transition
                                    {{ $selectedCategory === $key ? 'bg-brand-black100 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- SSS Listesi -->
            @if($faqs->count() > 0)
                <div class="space-y-4" x-data="{ openItem: null }">
                    @foreach($faqs as $faq)
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden transition hover:shadow-sm"
                             wire:key="faq-{{ $faq->id }}">
                            <button @click="openItem = openItem === {{ $faq->id }} ? null : {{ $faq->id }}"
                                    class="w-full flex items-center justify-between px-6 py-5 text-left">
                                <div class="flex items-start gap-4 flex-1">
                                    @if($faq->category)
                                        <span class="hidden sm:inline-flex px-2.5 py-1 text-[10px] font-medium bg-primary/10 text-primary rounded-full flex-shrink-0">
                                            {{ $faq->category_label }}
                                        </span>
                                    @endif
                                    <span class="font-medium text-brand-black100 text-sm md:text-base">{{ $faq->question }}</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform duration-200"
                                     :class="{ 'rotate-180': openItem === {{ $faq->id }} }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openItem === {{ $faq->id }}"
                                 x-collapse
                                 x-cloak>
                                <div class="px-6 pb-6 pt-0">
                                    <div class="text-gray-600 text-sm leading-relaxed pl-0 sm:pl-[72px] prose prose-sm max-w-none">
                                        {!! nl2br(e($faq->answer)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-400">Bu kategoride soru bulunmuyor.</p>
                </div>
            @endif

            <!-- Iletisim CTA -->
            <div class="mt-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-8 md:p-12 text-center">
                <h2 class="text-xl font-semibold text-brand-black100 mb-3">Hala sorunuz mu var?</h2>
                <p class="text-gray-500 text-sm mb-6 max-w-md mx-auto">
                    Aradiginiz cevabi bulamadiysaniz, musteri hizmetlerimiz size yardimci olmaktan mutluluk duyacaktir.
                </p>
                <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 bg-brand-black100 hover:bg-black text-white px-6 py-3 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Bize Ulasin
                </a>
            </div>
        </div>
    </div>
</div>
