<x-admin.layouts.app title="Kategori Duzenle">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Kategori Duzenle</h1>
        <a href="{{ route('admin.blog-categories.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Geri Don
        </a>
    </div>

    <form method="POST" action="{{ route('admin.blog-categories.update', $blogCategory) }}" class="max-w-2xl">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm p-6">
            <!-- Title -->
            <div class="mb-5">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Kategori Adi <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $blogCategory->title) }}" required
                       class="w-full border border-gray-300 rounded px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="Kategori adi...">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-5">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-gray-400">(bos birakilirsa otomatik olusturulur)</span></label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $blogCategory->slug) }}"
                       class="w-full border border-gray-300 rounded px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="kategori-slug">
                @error('slug')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-5">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $blogCategory->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 border-gray-300 rounded text-primary focus:ring-primary">
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>

            <!-- Info -->
            <div class="mb-5 p-3 bg-gray-50 rounded text-xs text-gray-500">
                <p>Yazi Sayisi: <strong class="text-gray-700">{{ $blogCategory->posts()->count() }}</strong></p>
                <p class="mt-1">Olusturulma: <strong class="text-gray-700">{{ $blogCategory->created_at->format('d.m.Y H:i') }}</strong></p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.blog-categories.index') }}" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm hover:bg-gray-50">Iptal</a>
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-yellow-600 text-white rounded-lg text-sm font-medium">Guncelle</button>
            </div>
        </div>
    </form>
</x-admin.layouts.app>
