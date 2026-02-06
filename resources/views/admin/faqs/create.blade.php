<x-admin.layouts.app title="Yeni SSS Ekle">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.faqs.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Yeni Soru Ekle</h1>
        </div>
    </div>

    <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Ana Icerik -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Soru <span class="text-red-500">*</span></label>
                        <input type="text" name="question" value="{{ old('question') }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 @error('question') border-red-500 @enderror"
                               placeholder="Ornegin: Siparisimi nasil takip edebilirim?">
                        @error('question')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cevap <span class="text-red-500">*</span></label>
                        <textarea name="answer" rows="8" required
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 @error('answer') border-red-500 @enderror"
                                  placeholder="Detayli cevabinizi buraya yazin...">{{ old('answer') }}</textarea>
                        @error('answer')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Yan Panel -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="category" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <option value="">Kategori Secin</option>
                            @foreach(\App\Models\Faq::CATEGORIES as $key => $label)
                                <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Siralama</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <p class="mt-1 text-xs text-gray-400">Kucuk sayi once gosterilir</p>
                    </div>

                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary/50">
                            <span class="text-sm font-medium text-gray-700">Aktif</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-400 ml-8">Pasif sorular sitede gosterilmez</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.faqs.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Iptal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-yellow-600 transition font-medium">
                Kaydet
            </button>
        </div>
    </form>
</x-admin.layouts.app>
