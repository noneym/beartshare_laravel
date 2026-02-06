<x-admin.layouts.app title="Eser Duzenle">
    <div class="mb-8">
        <a href="{{ route('admin.artworks.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Eserlere Don</a>
    </div>

    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Eser Duzenle: {{ $artwork->title }}</h1>

        <form action="{{ route('admin.artworks.update', $artwork) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sanatci *</label>
                    <select name="artist_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" required>
                        <option value="">Sanatci secin</option>
                        @foreach($artists as $artist)
                            <option value="{{ $artist->id }}" {{ old('artist_id', $artwork->artist_id) == $artist->id ? 'selected' : '' }}>{{ $artist->name }}</option>
                        @endforeach
                    </select>
                    @error('artist_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        <option value="">Kategori secin</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $artwork->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Eser Adi *</label>
                    <input type="text" name="title" value="{{ old('title', $artwork->title) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" required>
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aciklama</label>
                    <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">{{ old('description', $artwork->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teknik</label>
                        <input type="text" name="technique" value="{{ old('technique', $artwork->technique) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        @error('technique') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Boyutlar</label>
                        <input type="text" name="dimensions" value="{{ old('dimensions', $artwork->dimensions) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        @error('dimensions') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yil</label>
                    <input type="number" name="year" value="{{ old('year', $artwork->year) }}" min="1800" max="{{ date('Y') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    @error('year') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat (TL) *</label>
                        <input type="number" name="price_tl" value="{{ old('price_tl', $artwork->price_tl) }}" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" required>
                        @error('price_tl') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat (USD) *</label>
                        <input type="number" name="price_usd" value="{{ old('price_usd', $artwork->price_usd) }}" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" required>
                        @error('price_usd') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mevcut Gorseller</label>
                    @if($artwork->images && count($artwork->images) > 0)
                        <div class="flex gap-2 flex-wrap mb-4">
                            @foreach($artwork->images as $image)
                                <img src="{{ asset('storage/' . $image) }}" alt="" class="w-20 h-20 object-cover rounded-lg">
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mb-4">Gorsel yok</p>
                    @endif

                    <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Gorsel Ekle</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    <p class="text-sm text-gray-500 mt-1">Birden fazla gorsel secebilirsiniz</p>
                    @error('images.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $artwork->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Aktif</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ $artwork->is_featured ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">One Cikar</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_sold" value="1" {{ $artwork->is_sold ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Satildi</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_credit_card" value="1" {{ $artwork->allow_credit_card ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Kredi Karti ile Alinabilir</span>
                    </label>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.artworks.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Iptal</a>
                    <button type="submit" class="px-6 py-2 bg-primary hover:bg-yellow-600 text-white rounded-lg font-medium">Guncelle</button>
                </div>
            </div>
        </form>

        <!-- Favoriye Ekleyenler -->
        <div class="bg-white rounded-xl shadow-sm mt-8">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    Favoriye Ekleyenler
                </h2>
                <span class="text-xs text-gray-400 font-normal">{{ $favoritedBy->count() }} kullanici</span>
            </div>
            @if($favoritedBy->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($favoritedBy as $favUser)
                        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                    {{ strtoupper(mb_substr($favUser->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $favUser) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600 transition">{{ $favUser->name }}</a>
                                    <p class="text-xs text-gray-500">{{ $favUser->email }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $favUser->pivot->created_at ? \Carbon\Carbon::parse($favUser->pivot->created_at)->format('d.m.Y H:i') : '' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-8 text-center">
                    <p class="text-sm text-gray-400">Bu eseri henuz kimse favoriye eklemedi.</p>
                </div>
            @endif
        </div>
    </div>
</x-admin.layouts.app>
