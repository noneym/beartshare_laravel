<x-admin.layouts.app title="Yeni Blog Kategorisi">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Yeni Blog Kategorisi</h1>
        <a href="{{ route('admin.blog-categories.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Geri Don
        </a>
    </div>

    <form method="POST" action="{{ route('admin.blog-categories.store') }}" class="max-w-2xl">
        @csrf

        <div class="bg-white rounded-xl shadow-sm p-6">
            <!-- Title -->
            <div class="mb-5">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Kategori Adi <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="w-full border border-gray-300 rounded px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="Kategori adi...">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-5">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-gray-400">(bos birakilirsa otomatik olusturulur)</span></label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                       class="w-full border border-gray-300 rounded px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="kategori-slug">
                @error('slug')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-5">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 border-gray-300 rounded text-primary focus:ring-primary">
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.blog-categories.index') }}" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm hover:bg-gray-50">Iptal</a>
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-yellow-600 text-white rounded-lg text-sm font-medium">Kategori Olustur</button>
            </div>
        </div>
    </form>
</x-admin.layouts.app>
