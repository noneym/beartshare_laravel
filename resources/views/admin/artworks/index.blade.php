<x-admin.layouts.app title="Eserler">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Eserler</h1>
        <a href="{{ route('admin.artworks.create') }}" class="bg-primary hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium">
            + Yeni Eser
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.artworks.index') }}" class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <div class="flex flex-wrap items-end gap-3">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Eser adi, sanatci veya ID..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>

            <!-- Status -->
            <div class="min-w-[140px]">
                <label class="block text-xs text-gray-500 mb-1">Durum</label>
                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary bg-white">
                    <option value="">Tumu</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="passive" {{ request('status') === 'passive' ? 'selected' : '' }}>Pasif</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Satildi</option>
                    <option value="featured" {{ request('status') === 'featured' ? 'selected' : '' }}>One Cikan</option>
                    <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Rezerve</option>
                </select>
            </div>

            <!-- Artist (searchable) -->
            <x-admin.artist-filter :artists="$artists" />

            <!-- Category -->
            <div class="min-w-[140px]">
                <label class="block text-xs text-gray-500 mb-1">Kategori</label>
                <select name="category_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary bg-white">
                    <option value="">Tum Kategoriler</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Price Range -->
            <div class="min-w-[150px]">
                <label class="block text-xs text-gray-500 mb-1">Fiyat Araligi</label>
                <select name="price_range" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary bg-white">
                    <option value="">Tum Fiyatlar</option>
                    <option value="under_50k" {{ request('price_range') === 'under_50k' ? 'selected' : '' }}>50.000 TL Alti</option>
                    <option value="50k_100k" {{ request('price_range') === '50k_100k' ? 'selected' : '' }}>50.000 - 100.000 TL</option>
                    <option value="100k_500k" {{ request('price_range') === '100k_500k' ? 'selected' : '' }}>100.000 - 500.000 TL</option>
                    <option value="500k_1m" {{ request('price_range') === '500k_1m' ? 'selected' : '' }}>500.000 - 1.000.000 TL</option>
                    <option value="over_1m" {{ request('price_range') === 'over_1m' ? 'selected' : '' }}>1.000.000 TL Ustu</option>
                </select>
            </div>

            <!-- Sort -->
            <div class="min-w-[140px]">
                <label class="block text-xs text-gray-500 mb-1">Siralama</label>
                <select name="sort" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary bg-white">
                    <option value="">En Yeni</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>En Eski</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Fiyat (Artan)</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Fiyat (Azalan)</option>
                    <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Ada Gore (A-Z)</option>
                </select>
            </div>

            <!-- Gizleme seçenekleri (varsayılan işaretli) -->
            <input type="hidden" name="filtered" value="1">
            <div class="flex items-center gap-4 text-sm text-gray-700">
                <label class="inline-flex items-center gap-2 whitespace-nowrap">
                    <input type="checkbox" name="hide_sold" value="1" {{ $hideSold ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-primary focus:ring-primary">
                    Satılanları gizle
                </label>
                <label class="inline-flex items-center gap-2 whitespace-nowrap">
                    <input type="checkbox" name="hide_passive" value="1" {{ $hidePassive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-primary focus:ring-primary">
                    Pasifleri gizle
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700 transition">
                    Filtrele
                </button>
                @if(request()->hasAny(['search', 'status', 'artist_id', 'category_id', 'price_range', 'sort']))
                    <a href="{{ route('admin.artworks.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">
                        Temizle
                    </a>
                @endif
            </div>
        </div>

        <!-- Active Filter Tags -->
        @if(request()->hasAny(['search', 'status', 'artist_id', 'category_id', 'price_range']))
            <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-400">Aktif filtreler:</span>
                @if(request('search'))
                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full">
                        Arama: "{{ request('search') }}"
                    </span>
                @endif
                @if(request('status'))
                    <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 text-xs px-2 py-1 rounded-full">
                        {{ match(request('status')) { 'active' => 'Aktif', 'passive' => 'Pasif', 'sold' => 'Satildi', 'featured' => 'One Cikan', 'reserved' => 'Rezerve', default => request('status') } }}
                    </span>
                @endif
                @if(request('artist_id'))
                    <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs px-2 py-1 rounded-full">
                        {{ $artists->find(request('artist_id'))?->name ?? 'Sanatci' }}
                    </span>
                @endif
                @if(request('category_id'))
                    <span class="inline-flex items-center gap-1 bg-orange-50 text-orange-700 text-xs px-2 py-1 rounded-full">
                        {{ $categories->find(request('category_id'))?->name ?? 'Kategori' }}
                    </span>
                @endif
                @if(request('price_range'))
                    <span class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-700 text-xs px-2 py-1 rounded-full">
                        {{ match(request('price_range')) { 'under_50k' => '< 50K TL', '50k_100k' => '50K-100K TL', '100k_500k' => '100K-500K TL', '500k_1m' => '500K-1M TL', 'over_1m' => '> 1M TL', default => '' } }}
                    </span>
                @endif
            </div>
        @endif
    </form>

    <!-- Results Count -->
    <div class="flex items-center justify-between mb-3">
        <p class="text-sm text-gray-500">
            Toplam <span class="font-semibold text-gray-700">{{ $artworks->total() }}</span> eser
            @if($artworks->total() > 0)
                ({{ $artworks->firstItem() }}-{{ $artworks->lastItem() }} arasi gosteriliyor)
            @endif
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <x-admin.th sort="id" first="desc" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</x-admin.th>
                        <x-admin.th sort="title" first="asc" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Eser</x-admin.th>
                        <x-admin.th sort="artist" first="asc" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sanatci</x-admin.th>
                        <x-admin.th sort="category" first="asc" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</x-admin.th>
                        <x-admin.th sort="price" first="desc" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiyat</x-admin.th>
                        <x-admin.th sort="status" first="desc" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</x-admin.th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Islemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($artworks as $artwork)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono">
                                #{{ $artwork->id }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-16 h-12 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                                        @if($artwork->first_image)
                                            <img src="{{ $artwork->first_image_url }}" alt="{{ $artwork->title }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $artwork->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $artwork->technique }} {{ $artwork->dimensions ? '- ' . $artwork->dimensions : '' }} {{ $artwork->year ? '(' . $artwork->year . ')' : '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $artwork->artist->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $artwork->category->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900 text-sm">{{ $artwork->formatted_price_tl }}</p>
                                <p class="text-xs text-gray-500">{{ $artwork->formatted_price_usd }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($artwork->is_sold)
                                        <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-800">Satildi</span>
                                    @elseif($artwork->is_reserved)
                                        <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-800">Rezerve</span>
                                    @elseif($artwork->is_active)
                                        <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">Aktif</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-800">Pasif</span>
                                    @endif
                                    @if($artwork->is_featured)
                                        <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">Öne Çıkan</span>
                                    @endif
                                </div>
                                @if($artwork->is_featured)
                                    {{-- Sıra ağırlığı: listeden düzenlenir, değişince kaydedilir --}}
                                    <div class="flex items-center gap-1.5 mt-2"
                                         x-data="{ value: {{ (int) $artwork->featured_weight }}, state: '' }">
                                        <label class="text-[11px] text-gray-500" for="fw-{{ $artwork->id }}">Sıra ağırlığı</label>
                                        <input id="fw-{{ $artwork->id }}" type="number" min="0" max="100000" x-model.number="value"
                                               @change="state = 'saving';
                                                   fetch(@js(route('admin.artworks.featured-weight', $artwork)), {
                                                       method: 'PATCH',
                                                       headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                                                       body: JSON.stringify({ featured_weight: value || 0 })
                                                   }).then(r => { state = r.ok ? 'saved' : 'error'; if (r.ok) setTimeout(() => state = '', 1500); })
                                                     .catch(() => state = 'error')"
                                               class="w-16 border border-gray-300 rounded px-1.5 py-0.5 text-xs focus:outline-none focus:border-primary">
                                        <span x-show="state === 'saving'" class="text-[11px] text-gray-400">…</span>
                                        <span x-show="state === 'saved'" x-cloak class="text-[11px] text-green-600">Kaydedildi</span>
                                        <span x-show="state === 'error'" x-cloak class="text-[11px] text-red-600">Hata</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('artwork.detail', $artwork->slug) }}" target="_blank" class="text-gray-400 hover:text-gray-600" title="Sitede Gor">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <a href="{{ route('admin.artworks.edit', $artwork) }}" class="text-blue-600 hover:text-blue-800 text-sm">Duzenle</a>
                                    <form action="{{ route('admin.artworks.destroy', $artwork) }}" method="POST" class="inline" onsubmit="return confirm('{{ $artwork->title }} eserini silmek istediginize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    @if(request()->hasAny(['search', 'status', 'artist_id', 'category_id', 'price_range']))
                                        <p class="text-sm font-medium text-gray-500">Filtrelere uygun eser bulunamadi</p>
                                        <a href="{{ route('admin.artworks.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mt-1 inline-block">Filtreleri temizle</a>
                                    @else
                                        <p class="text-sm text-gray-500">Henuz eser eklenmemis.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($artworks->hasPages())
        <div class="mt-6">
            {{ $artworks->links() }}
        </div>
    @endif
</x-admin.layouts.app>
