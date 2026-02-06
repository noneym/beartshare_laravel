<x-admin.layouts.app title="Yeni Eser">
    <div class="mb-8">
        <a href="{{ route('admin.artworks.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Eserlere Don</a>
    </div>

    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Yeni Eser Ekle</h1>

        <form action="{{ route('admin.artworks.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sanatci *</label>
                    <select name="artist_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" required>
                        <option value="">Sanatci secin</option>
                        @foreach($artists as $artist)
                            <option value="{{ $artist->id }}" {{ old('artist_id') == $artist->id ? 'selected' : '' }}>{{ $artist->name }}</option>
                        @endforeach
                    </select>
                    @error('artist_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        <option value="">Kategori secin</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Eser Adi *</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" required>
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aciklama</label>
                    <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teknik</label>
                        <input type="text" name="technique" value="{{ old('technique') }}" placeholder="Tuval uzerine yagliboya" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        @error('technique') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Boyutlar</label>
                        <input type="text" name="dimensions" value="{{ old('dimensions') }}" placeholder="100 x 120 cm" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        @error('dimensions') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yil</label>
                    <input type="number" name="year" value="{{ old('year') }}" min="1800" max="{{ date('Y') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    @error('year') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat (TL) *</label>
                        <input type="number" name="price_tl" value="{{ old('price_tl') }}" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" required>
                        @error('price_tl') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat (USD) *</label>
                        <input type="number" name="price_usd" value="{{ old('price_usd') }}" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" required>
                        @error('price_usd') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gorseller</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    <p class="text-sm text-gray-500 mt-1">Birden fazla gorsel secebilirsiniz</p>
                    @error('images.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Aktif</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">One Cikar</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_sold" value="1" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Satildi</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_credit_card" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Kredi Karti ile Alinabilir</span>
                    </label>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.artworks.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Iptal</a>
                    <button type="submit" class="px-6 py-2 bg-primary hover:bg-yellow-600 text-white rounded-lg font-medium">Kaydet</button>
                </div>
            </div>
        </form>
    </div>
</x-admin.layouts.app>
