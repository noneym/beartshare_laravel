<x-layouts.app
    title="Sayfa Bulunamadı | BeArtShare"
    metaDescription="Aradığınız sayfa bulunamadı. BeArtShare'de eserleri ve sanatçıları keşfedin."
    metaRobots="noindex, follow"
>
    <section class="container mx-auto px-4 py-24 md:py-32">
        <div class="max-w-xl mx-auto text-center">
            <p class="text-xs tracking-[0.3em] uppercase text-gray-400 mb-4">404</p>
            <h1 class="text-3xl md:text-4xl font-semibold text-brand-black100 mb-4">Sayfa bulunamadı</h1>
            <p class="text-gray-500 text-sm leading-relaxed mb-10">
                Aradığınız sayfa taşınmış ya da kaldırılmış olabilir. Satıştaki eserlere göz atabilir veya sanatçıları keşfedebilirsiniz.
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('artworks') }}" class="inline-flex items-center bg-brand-black100 text-white px-6 py-3 text-sm font-medium hover:bg-black transition">Eserleri Keşfet</a>
                <a href="{{ route('artists') }}" class="inline-flex items-center border border-gray-200 text-brand-black100 px-6 py-3 text-sm hover:border-brand-black100 transition">Sanatçılar</a>
                <a href="{{ route('home') }}" class="inline-flex items-center border border-gray-200 text-brand-black100 px-6 py-3 text-sm hover:border-brand-black100 transition">Ana Sayfa</a>
            </div>
        </div>
    </section>
</x-layouts.app>
