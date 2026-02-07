<x-admin.layouts.app>
    <x-slot name="title">Yeni Kategori</x-slot>

    {{-- Baslik --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Yeni Kategori</h1>
            <p class="text-sm text-gray-500 mt-1">Yeni bir eser kategorisi olusturun</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:text-gray-800 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Geri Don
        </a>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6">
            @csrf

            <div class="space-y-6">
                {{-- Kategori Adi --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori Adi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
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
                              placeholder="Kategori hakkinda kisa bir aciklama...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Aktif Mi --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
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
                    Kategori Olustur
                </button>
            </div>
        </form>
    </div>

</x-admin.layouts.app>
