<div>
    <!-- Page Header -->
    <section class="bg-brand-black100 py-16">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-light text-white">Bl<span class="font-semibold">og</span></h1>
            <p class="text-white/50 text-sm mt-2">Sanat dünyasından haberler, yazılar ve daha fazlası</p>
        </div>
    </section>

    <div class="container mx-auto px-4 py-10">
        <!-- Filters -->
        <div class="border-b border-gray-100 pb-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
                <!-- Search -->
                <div class="relative flex-1 max-w-md">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Blog yazılarında ara..."
                        class="w-full border border-gray-200 px-4 py-2.5 pl-10 text-sm focus:outline-none focus:border-brand-black100 transition"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Category Tabs -->
                <div class="flex flex-wrap gap-2">
                    <button
                        wire:click="$set('selectedCategory', '')"
                        class="px-4 py-2 text-sm border transition {{ $selectedCategory === '' ? 'bg-brand-black100 text-white border-brand-black100' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}"
                    >
                        Tümü
                    </button>
                    @foreach($categories as $category)
                        <button
                            wire:click="$set('selectedCategory', '{{ $category->slug }}')"
                            class="px-4 py-2 text-sm border transition {{ $selectedCategory === $category->slug ? 'bg-brand-black100 text-white border-brand-black100' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}"
                        >
                            {{ $category->title }}
                            <span class="ml-1 text-xs opacity-60">({{ $category->posts_count }})</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Blog Posts Grid -->
        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article class="group">
                        <a href="{{ route('blog.detail', $post->slug) }}" class="block">
                            <!-- Image -->
                            <div class="aspect-[16/10] overflow-hidden bg-gray-100 mb-4">
                                <img
                                    src="{{ $post->image_url }}"
                                    alt="{{ $post->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                    onerror="this.src='https://via.placeholder.com/600x400/f3f4f6/9ca3af?text=BeArtShare'"
                                >
                            </div>

                            <!-- Content -->
                            <div>
                                @if($post->category)
                                    <span class="text-xs font-medium text-primary uppercase tracking-wider">
                                        {{ $post->category->title }}
                                    </span>
                                @endif

                                <h2 class="text-lg font-semibold text-brand-black100 mt-1 mb-2 group-hover:text-primary transition line-clamp-2">
                                    {{ $post->title }}
                                </h2>

                                <p class="text-sm text-gray-500 line-clamp-3 mb-3">
                                    {{ $post->excerpt }}
                                </p>

                                <div class="flex items-center text-xs text-gray-400 gap-3">
                                    <span>{{ $post->created_at->translatedFormat('d F Y') }}</span>
                                    <span>&middot;</span>
                                    <span>{{ $post->read_time }}</span>
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <p class="text-gray-400 text-lg">Henüz blog yazısı bulunamadı</p>
                @if($search || $selectedCategory)
                    <p class="text-gray-400 text-sm mt-2">Farklı filtreler deneyebilirsiniz</p>
                    <button wire:click="$set('search', ''); $set('selectedCategory', '')" class="mt-4 text-primary hover:underline text-sm">
                        Filtreleri Temizle
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
