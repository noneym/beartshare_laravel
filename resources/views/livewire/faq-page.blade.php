<div>
    <!-- Hero -->
    <div class="bg-gray-50 border-b border-gray-100">
        <div class="container mx-auto px-4 py-12 text-center">
            <h1 class="text-3xl md:text-4xl font-semibold text-brand-black100 mb-3">
                Sıkça Sorulan Sorular
            </h1>
            <p class="text-gray-400 text-sm max-w-xl mx-auto">
                BeArtShare hakkında merak ettiğiniz her şeyin cevabı burada. Aradığınızı bulamadıysanız bizimle iletişime geçebilirsiniz.
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Kategori Filtreleri -->
            @if($categories->count() > 0)
                <div class="flex flex-wrap justify-center gap-2 mb-10">
                    <button wire:click="setCategory('')"
                            class="px-4 py-2 text-sm  transition
                                {{ $selectedCategory === '' ? 'bg-brand-black100 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Tümü
                    </button>
                    @foreach($categories as $key => $label)
                        <button wire:click="setCategory('{{ $key }}')"
                                class="px-4 py-2 text-sm  transition
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
                        <div class="bg-white border border-gray-200  overflow-hidden transition hover:shadow-sm"
                             wire:key="faq-{{ $faq->id }}">
                            <button @click="openItem = openItem === {{ $faq->id }} ? null : {{ $faq->id }}"
                                    class="w-full flex items-center justify-between px-6 py-5 text-left">
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <span class="font-medium text-brand-black100 text-sm md:text-base flex-1">{{ $faq->question }}</span>
                                    @if($faq->category)
                                        <span class="hidden sm:inline-flex px-2.5 py-1 text-[10px] font-medium bg-primary/10 text-primary  flex-shrink-0">
                                            {{ $faq->category_label }}
                                        </span>
                                    @endif
                                </div>
                                <i class="ph ph-caret-down text-xl text-gray-400 flex-shrink-0 ml-4 transition-transform duration-200"
                                   :class="{ 'rotate-180': openItem === {{ $faq->id }} }"></i>
                            </button>
                            <div x-show="openItem === {{ $faq->id }}"
                                 x-collapse
                                 x-cloak>
                                <div class="px-6 pb-6 pt-0">
                                    <div class="text-gray-600 text-sm leading-relaxed prose prose-sm max-w-none">
                                        {!! nl2br(e($faq->answer)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <i class="ph ph-question text-4xl text-gray-300"></i>
                    <p class="text-gray-400">Bu kategoride soru bulunmuyor.</p>
                </div>
            @endif

            <!-- İletişim CTA -->
            <div class="mt-16 bg-gray-50 border border-gray-100  p-8 md:p-12 text-center">
                <h2 class="text-xl font-semibold text-brand-black100 mb-3">Hâlâ sorunuz mu var?</h2>
                <p class="text-gray-500 text-sm mb-6 max-w-md mx-auto">
                    Aradığınız cevabı bulamadıysanız, müşteri hizmetlerimiz size yardımcı olmaktan mutluluk duyacaktır.
                </p>
                <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 bg-brand-black100 hover:bg-black text-white px-6 py-3  text-sm font-medium transition">
                    <i class="ph ph-envelope-simple text-lg"></i>
                    Bize Ulaşın
                </a>
            </div>
        </div>
    </div>
</div>
