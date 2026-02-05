<x-admin.layouts.app title="Yeni Sanatci">
    <div class="mb-8">
        <a href="{{ route('admin.artists.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Sanatcilara Don</a>
    </div>

    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Yeni Sanatci Ekle</h1>

        <form action="{{ route('admin.artists.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" required>
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dogum Yili</label>
                        <input type="number" name="birth_year" value="{{ old('birth_year') }}" min="1800" max="{{ date('Y') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        @error('birth_year') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Olum Yili</label>
                        <input type="number" name="death_year" value="{{ old('death_year') }}" min="1800" max="{{ date('Y') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        @error('death_year') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Biyografi</label>
                    <textarea name="biography" rows="5" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">{{ old('biography') }}</textarea>
                    @error('biography') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fotograf</label>
                    <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Aktif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.artists.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Iptal</a>
                    <button type="submit" class="px-6 py-2 bg-primary hover:bg-yellow-600 text-white rounded-lg font-medium">Kaydet</button>
                </div>
            </div>
        </form>
    </div>
</x-admin.layouts.app>
