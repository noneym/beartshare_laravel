<x-layouts.app title="Blog - BeArtShare">
    <!-- Page Header -->
    <section class="bg-brand-black100 py-16">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-light text-white"><span class="font-semibold">Blog</span></h1>
            <p class="text-white/50 text-sm mt-2">Sanat dünyasından haberler ve yazılar</p>
        </div>
    </section>

    <div class="container mx-auto px-4 py-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
            @for($i = 1; $i <= 6; $i++)
                <article class="group">
                    <div class="bg-gray-50 overflow-hidden aspect-video mb-4">
                        <div class="w-full h-full flex items-center justify-center text-gray-300 group-hover:bg-gray-100 transition">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider">Sanat Haberleri</span>
                        <h2 class="text-sm font-medium text-brand-black100 group-hover:underline transition">
                            Blog Yazısı Başlığı {{ $i }}
                        </h2>
                        <p class="text-gray-400 text-xs leading-relaxed line-clamp-2">
                            Sanat dünyasından en güncel haberler ve yazılar. Türk ve dünya sanatından ilham veren içerikler.
                        </p>
                        <div class="flex items-center text-[10px] text-gray-300 pt-1">
                            <span>{{ now()->subDays($i)->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>
                </article>
            @endfor
        </div>
    </div>
</x-layouts.app>
