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
                    <x-admin.artist-select :artists="$artists" :selected="old('artist_id')" />
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ek Not <span class="text-gray-400 font-normal">(isteğe bağlı)</span></label>
                    <input type="text" name="extra_note" value="{{ old('extra_note') }}" placeholder="Ör. Eserin Evin Sanat Galerisi tarafından sertifikası mevcuttur" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    <p class="text-xs text-gray-500 mt-1">Boyut alanına yalnızca ölçü yazın; sertifika, literatür, edisyon gibi bilgiler buraya. Eser listelerinde ve eser sayfasında boyutun altında görünür.</p>
                    @error('extra_note') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yil</label>
                    <input type="number" name="year" value="{{ old('year') }}" min="1800" max="{{ date('Y') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    @error('year') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat (TL) *</label>
                        <input type="text" inputmode="decimal" data-price-format value="{{ old('price_tl') ? number_format((float) old('price_tl'), 2, ',', '.') : '' }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" placeholder="100.000" required>
                        <input type="hidden" name="price_tl" value="{{ old('price_tl') }}">
                        <p class="text-gray-400 text-xs mt-1">Örn. 100.000 veya 100.000,50</p>
                        @error('price_tl') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat (USD)</label>
                        <input type="text" inputmode="decimal" data-price-format value="{{ old('price_usd') ? number_format((float) old('price_usd'), 2, ',', '.') : '' }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" placeholder="3.500">
                        <p class="text-gray-400 text-xs mt-1">TL fiyatı ve güncel TCMB kuru ile otomatik hesaplanır (saatlik güncellenir).</p>
                        <input type="hidden" name="price_usd" value="{{ old('price_usd') }}">
                        <p class="text-gray-400 text-xs mt-1">Örn. 3.500 veya 3.500,50</p>
                        @error('price_usd') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                @include('admin.partials.price-format-script')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gorseller</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    <p class="text-sm text-gray-500 mt-1">Birden fazla gorsel secebilirsiniz</p>
                    @error('images.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kapak Fotoğrafı <span class="text-gray-400 font-normal">(isteğe bağlı)</span></label>
                    <input type="file" name="cover" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    <p class="text-sm text-gray-500 mt-1">Yüklenirse eser listelerinde (ana sayfa, eserler, sanatçı, favoriler) ilk görselin yerine bu gösterilir. Eser sayfasındaki görseller değişmez.</p>
                    @error('cover') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
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
                        <span class="ml-2 text-gray-700">Kredi Kartı ile Alınabilir</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="hide_from_gallery" value="1" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Sanal Galeri Dışı Bırak</span>
                        <span class="ml-2 text-xs text-gray-400">(3D sergilerde gösterilmez)</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Satış Notu</label>
                    <textarea name="sale_note" rows="3" placeholder="Eser sayfasında fiyatın altında gösterilir (ör. komisyon faturası / KDV bilgisi)" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">{{ old('sale_note') }}</textarea>
                    @error('sale_note') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-gray-200 pt-6 space-y-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Yalnızca Admin Görür</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Asıl Sahibi (Konsinye)</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name') }}" placeholder="Eser konsinye ise sahibinin adı" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        <p class="text-gray-400 text-xs mt-1">Sitede gösterilmez.</p>
                        @error('owner_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notu</label>
                        <textarea name="admin_notes" rows="3" placeholder="Dahili not (anlaşma şartları, iletişim, vb.)" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">{{ old('admin_notes') }}</textarea>
                        @error('admin_notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.artworks.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Iptal</a>
                    <button type="submit" class="px-6 py-2 bg-primary hover:bg-yellow-600 text-white rounded-lg font-medium">Kaydet</button>
                </div>
            </div>
        </form>
    </div>
</x-admin.layouts.app>
