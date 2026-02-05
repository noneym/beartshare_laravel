<x-admin.layouts.app title="Sanatcilar">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Sanatcilar</h1>
        <a href="{{ route('admin.artists.create') }}" class="bg-primary hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium">
            + Yeni Sanatci
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sanatci</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dogum/Olum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Eser Sayisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Islemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($artists as $artist)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded-full overflow-hidden flex-shrink-0">
                                    @if($artist->avatar_url)
                                        <img src="{{ $artist->avatar_url }}" alt="{{ $artist->name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <p class="font-medium text-gray-900">{{ $artist->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $artist->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $artist->life_span }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $artist->artworks_count }} eser
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 text-xs rounded-full {{ $artist->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $artist->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.artists.edit', $artist) }}" class="text-blue-600 hover:text-blue-800 mr-3">Duzenle</a>
                            <form action="{{ route('admin.artists.destroy', $artist) }}" method="POST" class="inline" onsubmit="return confirm('Silmek istediginize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Henuz sanatci eklenmemis.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $artists->links() }}
    </div>
</x-admin.layouts.app>
