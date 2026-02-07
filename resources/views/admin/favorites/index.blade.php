<x-admin.layouts.app>
    <x-slot name="title">Kullanici Favorileri</x-slot>

    {{-- Baslik --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kullanici Favorileri</h1>
            <p class="text-sm text-gray-500 mt-1">Tum kullanicilarin favori eserleri</p>
        </div>
    </div>

    {{-- Istatistik Kartlari --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Toplam Favori</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Favori Ekleyen</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['users_with_favorites']) }}</p>
            <p class="text-[10px] text-gray-400">kullanici</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Favorilenen</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($stats['artworks_favorited']) }}</p>
            <p class="text-[10px] text-gray-400">eser</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Bugun</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['today']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Bu Hafta</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ number_format($stats['this_week']) }}</p>
        </div>
    </div>

    {{-- En Cok Favorilenen Eserler --}}
    @if($topArtworks->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">En Cok Favorilenen Eserler</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($topArtworks as $item)
                <a href="{{ route('admin.artworks.edit', $item->artwork_id) }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-full hover:bg-gray-200 transition text-sm">
                    <span class="text-gray-700">{{ $item->artwork?->title ?? 'Silinmis Eser' }}</span>
                    <span class="bg-primary text-white text-xs px-2 py-0.5 rounded-full">{{ $item->count }}</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filtreler --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.favorites.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kullanici, eser, sanatci..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div class="min-w-[180px]">
                <label class="block text-xs text-gray-500 mb-1">Siralama</label>
                <select name="sort" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>En Yeni Favori</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>En Eski Favori</option>
                    <option value="artwork" {{ request('sort') === 'artwork' ? 'selected' : '' }}>Eser (A-Z)</option>
                    <option value="artwork_desc" {{ request('sort') === 'artwork_desc' ? 'selected' : '' }}>Eser (Z-A)</option>
                    <option value="user" {{ request('sort') === 'user' ? 'selected' : '' }}>Kullanici (A-Z)</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700 transition">
                Filtrele
            </button>
            @if(request()->hasAny(['search', 'sort']))
                <a href="{{ route('admin.favorites.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">
                    Temizle
                </a>
            @endif
        </form>
    </div>

    {{-- Tablo --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eser</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sanatci</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanici</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eklenme Tarihi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($favorites as $favorite)
                    <tr class="hover:bg-gray-50">
                        {{-- Eser --}}
                        <td class="px-4 py-3">
                            @if($favorite->artwork)
                                <div class="flex items-center gap-3">
                                    @if($favorite->artwork->first_image_url)
                                        <img src="{{ $favorite->artwork->first_image_url }}"
                                             alt="{{ $favorite->artwork->title }}"
                                             class="w-12 h-12 object-cover rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.artworks.edit', $favorite->artwork) }}"
                                           class="text-sm font-medium text-gray-900 hover:text-primary">
                                            {{ Str::limit($favorite->artwork->title, 30) }}
                                        </a>
                                        <p class="text-xs text-gray-400">ID: {{ $favorite->artwork->id }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">Silinmis Eser</span>
                            @endif
                        </td>

                        {{-- Sanatci --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($favorite->artwork?->artist)
                                <a href="{{ route('admin.artists.edit', $favorite->artwork->artist) }}"
                                   class="text-sm text-gray-700 hover:text-primary">
                                    {{ $favorite->artwork->artist->name }}
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>

                        {{-- Kullanici --}}
                        <td class="px-4 py-3">
                            @if($favorite->user)
                                <div>
                                    <a href="{{ route('admin.users.show', $favorite->user) }}"
                                       class="text-sm font-medium text-gray-900 hover:text-primary">
                                        {{ $favorite->user->name }}
                                    </a>
                                    <p class="text-xs text-gray-400">{{ $favorite->user->email }}</p>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">Silinmis Kullanici</span>
                            @endif
                        </td>

                        {{-- Fiyat --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($favorite->artwork)
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $favorite->artwork->formatted_price_tl }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        {{-- Durum --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($favorite->artwork)
                                @if($favorite->artwork->is_sold)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Satildi
                                    </span>
                                @elseif($favorite->artwork->is_reserved)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Rezerve
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Satilikta
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                    Silinmis
                                </span>
                            @endif
                        </td>

                        {{-- Eklenme Tarihi --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $favorite->created_at->format('d.m.Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $favorite->created_at->format('H:i') }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">
                            Henuz favori eklenmemis.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($favorites->hasPages())
        <div class="mt-4">
            {{ $favorites->links() }}
        </div>
    @endif

</x-admin.layouts.app>
