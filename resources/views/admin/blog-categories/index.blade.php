<x-admin.layouts.app title="Blog Kategorileri">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Blog Kategorileri</h1>
        <a href="{{ route('admin.blog-categories.create') }}" class="bg-primary hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Yeni Kategori
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kategori ara..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">Tum Durumlar</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="passive" {{ request('status') === 'passive' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700 transition">Filtrele</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.blog-categories.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2">Temizle</a>
            @endif
        </div>
    </form>

    <!-- Results Info -->
    <div class="mb-4 text-sm text-gray-500">
        Toplam {{ $categories->total() }} kategori
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kategori Adi</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Slug</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Yazi Sayisi</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Durum</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Islemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-400 font-mono text-xs">#{{ $category->id }}</td>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $category->title }}</td>
                        <td class="px-6 py-3 text-gray-500 font-mono text-xs">{{ $category->slug }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $category->posts_count }} yazi
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Pasif</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.blog-categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Duzenle</a>
                                <form method="POST" action="{{ route('admin.blog-categories.destroy', $category) }}" class="inline"
                                      onsubmit="return confirm('Bu kategoriyi silmek istediginize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Sil</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <p class="text-sm">Henuz blog kategorisi yok.</p>
                            <a href="{{ route('admin.blog-categories.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">Ilk kategoriyi olusturun</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="mt-6">{{ $categories->links() }}</div>
    @endif
</x-admin.layouts.app>
