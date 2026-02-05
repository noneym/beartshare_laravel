<x-admin.layouts.app title="Blog Yazilari">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Blog Yazilari</h1>
        <a href="{{ route('admin.blog-posts.create') }}" class="bg-primary hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Yeni Yazi
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Yazi ara (baslik, ID)..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">Tum Durumlar</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="passive" {{ request('status') === 'passive' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
            <div>
                <select name="category_id" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">Tum Kategoriler</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="sort" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">En Yeni</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>En Eski</option>
                    <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Basliga Gore</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700 transition">Filtrele</button>
            @if(request()->hasAny(['search', 'status', 'category_id', 'sort']))
                <a href="{{ route('admin.blog-posts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2">Temizle</a>
            @endif
        </div>
    </form>

    <!-- Active Filters -->
    @if(request()->hasAny(['search', 'status', 'category_id']))
        <div class="flex flex-wrap gap-2 mb-4">
            @if(request('search'))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs border border-blue-200">
                    Arama: {{ request('search') }}
                    <a href="{{ request()->fullUrlWithoutQuery('search') }}" class="hover:text-blue-900">&times;</a>
                </span>
            @endif
            @if(request('status'))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full text-xs border border-purple-200">
                    {{ request('status') === 'active' ? 'Aktif' : 'Pasif' }}
                    <a href="{{ request()->fullUrlWithoutQuery('status') }}" class="hover:text-purple-900">&times;</a>
                </span>
            @endif
            @if(request('category_id'))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-xs border border-green-200">
                    {{ $categories->firstWhere('id', request('category_id'))?->title }}
                    <a href="{{ request()->fullUrlWithoutQuery('category_id') }}" class="hover:text-green-900">&times;</a>
                </span>
            @endif
        </div>
    @endif

    <!-- Results Info -->
    <div class="mb-4 text-sm text-gray-500">
        Toplam {{ $posts->total() }} yazi
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Gorsel</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Baslik</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Yazar</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Durum</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tarih</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Islemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($posts as $post)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-400 font-mono text-xs">#{{ $post->id }}</td>
                        <td class="px-6 py-3">
                            @if($post->image)
                                <img src="{{ $post->image_url }}" alt="" class="w-12 h-8 object-cover rounded">
                            @else
                                <div class="w-12 h-8 bg-gray-100 rounded flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-900 truncate max-w-xs">{{ $post->title }}</p>
                            <p class="text-xs text-gray-400 font-mono truncate">{{ $post->slug }}</p>
                        </td>
                        <td class="px-6 py-3">
                            @if($post->category)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $post->category->title }}</span>
                            @else
                                <span class="text-gray-400 text-xs">Kategorisiz</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $post->user?->name ?? '-' }}</td>
                        <td class="px-6 py-3">
                            @if($post->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Pasif</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $post->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('blog.detail', $post->slug) }}" target="_blank" class="text-gray-400 hover:text-gray-600 text-xs" title="Sitede Gor">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                <a href="{{ route('admin.blog-posts.edit', $post) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Duzenle</a>
                                <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}" class="inline"
                                      onsubmit="return confirm('Bu yaziyi silmek istediginize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Sil</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            @if(request()->hasAny(['search', 'status', 'category_id']))
                                <p class="text-sm">Filtrelere uygun yazi bulunamadi.</p>
                                <a href="{{ route('admin.blog-posts.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">Filtreleri temizle</a>
                            @else
                                <p class="text-sm">Henuz blog yazisi yok.</p>
                                <a href="{{ route('admin.blog-posts.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">Ilk yaziyi olusturun</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
        <div class="mt-6">{{ $posts->links() }}</div>
    @endif
</x-admin.layouts.app>
