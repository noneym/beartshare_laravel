<x-admin.layouts.app title="Eserler">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Eserler</h1>
        <a href="{{ route('admin.artworks.create') }}" class="bg-primary hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium">
            + Yeni Eser
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Eser</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sanatci</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiyat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Islemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($artworks as $artwork)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-16 h-12 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                                    @if($artwork->first_image)
                                        <img src="{{ $artwork->first_image_url }}" alt="{{ $artwork->title }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <p class="font-medium text-gray-900">{{ $artwork->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $artwork->dimensions }} - {{ $artwork->year }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $artwork->artist->name }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $artwork->formatted_price_tl }}</p>
                            <p class="text-sm text-gray-500">{{ $artwork->formatted_price_usd }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($artwork->is_sold)
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Satildi</span>
                            @elseif($artwork->is_active)
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Pasif</span>
                            @endif
                            @if($artwork->is_featured)
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 ml-1">One Cikan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.artworks.edit', $artwork) }}" class="text-blue-600 hover:text-blue-800 mr-3">Duzenle</a>
                            <form action="{{ route('admin.artworks.destroy', $artwork) }}" method="POST" class="inline" onsubmit="return confirm('Silmek istediginize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Henuz eser eklenmemis.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $artworks->links() }}
    </div>
</x-admin.layouts.app>
