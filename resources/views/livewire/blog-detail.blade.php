<div>
    <article class="container mx-auto px-4 max-w-4xl pt-10 pb-16 md:pt-14">
        <nav class="text-sm text-gray-400 mb-6 flex items-center gap-2 min-w-0">
            <a href="{{ route('blog') }}" class="hover:text-brand-black100 transition flex-shrink-0">Haberler</a>
            @if($post->category)
                <span>/</span>
                <a href="{{ route('blog', ['kategori' => $post->category->slug]) }}" class="hover:text-brand-black100 transition truncate">{{ $post->category->title }}</a>
            @endif
        </nav>

        <h1 class="text-3xl md:text-5xl font-semibold tracking-tight leading-[1.1] text-brand-black100 max-w-[22ch]">{{ $post->title }}</h1>
        <p class="mt-4 text-sm text-gray-400 italic">{{ $post->created_at->format('d.m.Y') }}</p>

        @if($post->image)
            <div class="aspect-[16/9] overflow-hidden bg-gray-100 mt-8">
                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover" onerror="this.parentElement.style.display='none'">
            </div>
        @endif

        <div class="prose prose-lg max-w-none mt-10
            prose-headings:text-brand-black100 prose-headings:font-semibold prose-headings:tracking-tight
            prose-p:text-gray-600 prose-p:leading-relaxed
            prose-a:text-brand-black100 prose-a:underline prose-a:underline-offset-4 hover:prose-a:text-primary
            prose-img:rounded-none
            prose-strong:text-brand-black100
            prose-blockquote:border-primary prose-blockquote:text-gray-500">
            {!! $post->content !!}
        </div>

        <div class="border-t border-gray-100 mt-12 pt-6 flex items-center justify-between flex-wrap gap-4">
            <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-brand-black100 transition">
                <i class="ph ph-arrow-left"></i> Tüm haberler
            </a>
            <div class="flex items-center gap-1">
                <span class="text-sm text-gray-400 mr-2">Paylaş</span>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" aria-label="X'te paylaş" class="w-9 h-9 inline-flex items-center justify-center text-gray-500 hover:text-brand-black100 hover:bg-gray-100 transition"><i class="ph ph-x-logo text-lg"></i></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" aria-label="Facebook'ta paylaş" class="w-9 h-9 inline-flex items-center justify-center text-gray-500 hover:text-brand-black100 hover:bg-gray-100 transition"><i class="ph ph-facebook-logo text-lg"></i></a>
                <a href="https://wa.me/?text={{ urlencode($post->title . ' - ' . request()->url()) }}" target="_blank" rel="noopener" aria-label="WhatsApp'ta paylaş" class="w-9 h-9 inline-flex items-center justify-center text-gray-500 hover:text-brand-black100 hover:bg-gray-100 transition"><i class="ph ph-whatsapp-logo text-lg"></i></a>
            </div>
        </div>
    </article>

    @if($relatedPosts->count() > 0)
        <section class="border-t border-gray-100 bg-gray-50">
            <div class="container mx-auto px-4 max-w-4xl py-12">
                <h2 class="text-2xl font-semibold tracking-tight text-brand-black100 mb-6">İlgili haberler</h2>
                <div class="divide-y divide-gray-200">
                    @foreach($relatedPosts as $related)
                        <a href="{{ route('blog.detail', $related->slug) }}" class="group flex gap-5 py-5 first:pt-0 last:pb-0">
                            <div class="w-32 h-20 flex-shrink-0 overflow-hidden bg-gray-200">
                                <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-medium text-brand-black100 group-hover:text-primary transition line-clamp-2 leading-snug">{{ $related->title }}</h3>
                                <p class="mt-1.5 text-sm text-gray-400 italic">{{ $related->created_at->format('d.m.Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
