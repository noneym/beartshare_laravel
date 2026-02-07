<x-admin.layouts.app>
    <x-slot name="title">Kategori Duzenle</x-slot>

    {{-- Baslik --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kategori Duzenle</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $category->name }}</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:text-gray-800 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Geri Don
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- Kategori Adi --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Kategori Adi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary @error('name') border-red-500 @enderror"
                                   placeholder="Ornegin: Yagli Boya">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Aciklama --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Aciklama
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary @error('description') border-red-500 @enderror"
                                      placeholder="Kategori hakkinda kisa bir aciklama...">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Aktif Mi --}}
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            <label for="is_active" class="text-sm text-gray-700">
                                Aktif (sitede gorunur)
                            </label>
                        </div>
                    </div>

                    {{-- Butonlar --}}
                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">
                            Iptal
                        </a>
                        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-lg font-medium transition">
                            Degisiklikleri Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bilgi Paneli --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Kategori Bilgileri</h3>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500">Slug</p>
                        <code class="text-sm text-gray-700 bg-gray-100 px-2 py-1 rounded block mt-1">{{ $category->slug }}</code>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Eser Sayisi</p>
                        <p class="text-lg font-semibold text-gray-800 mt-1">{{ $category->artworks_count }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Olusturulma Tarihi</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $category->created_at->format('d.m.Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Son Guncelleme</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $category->updated_at->format('d.m.Y H:i') }}</p>
                    </div>

                    @if($category->artworks_count > 0)
                        <div class="pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.artworks.index', ['category_id' => $category->id]) }}"
                               class="text-primary hover:text-primary/80 text-sm font-medium flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Eserleri Gor
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tehlikeli Bolge --}}
            @if($category->artworks_count == 0)
                <div class="bg-red-50 rounded-lg shadow p-6 mt-6 border border-red-200">
                    <h3 class="text-sm font-semibold text-red-700 mb-2">Tehlikeli Bolge</h3>
                    <p class="text-xs text-red-600 mb-4">Bu islemi geri alamazsiniz.</p>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                          onsubmit="return confirm('Bu kategoriyi silmek istediginize emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Kategoriyi Sil
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

</x-admin.layouts.app>
