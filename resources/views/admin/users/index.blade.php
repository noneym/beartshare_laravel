<x-admin.layouts.app title="Kullanicilar">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Kullanicilar</h1>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <div class="flex flex-wrap items-end gap-3">
            <!-- Search -->
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs text-gray-500 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Ad, e-posta, telefon, TC, ID veya referans kodu..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>

            <!-- Role -->
            <div class="min-w-[120px]">
                <label class="block text-xs text-gray-500 mb-1">Rol</label>
                <select name="role" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary bg-white">
                    <option value="">Tumu</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Kullanici</option>
                </select>
            </div>

            <!-- Has Orders -->
            <div class="min-w-[130px]">
                <label class="block text-xs text-gray-500 mb-1">Siparis</label>
                <select name="has_orders" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary bg-white">
                    <option value="">Tumu</option>
                    <option value="yes" {{ request('has_orders') === 'yes' ? 'selected' : '' }}>Siparis Var</option>
                    <option value="no" {{ request('has_orders') === 'no' ? 'selected' : '' }}>Siparis Yok</option>
                </select>
            </div>

            <!-- Has ArtPuan -->
            <div class="min-w-[130px]">
                <label class="block text-xs text-gray-500 mb-1">ArtPuan</label>
                <select name="has_artpuan" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary bg-white">
                    <option value="">Tumu</option>
                    <option value="yes" {{ request('has_artpuan') === 'yes' ? 'selected' : '' }}>ArtPuan Var</option>
                    <option value="no" {{ request('has_artpuan') === 'no' ? 'selected' : '' }}>ArtPuan Yok</option>
                </select>
            </div>

            <!-- Sort -->
            <div class="min-w-[140px]">
                <label class="block text-xs text-gray-500 mb-1">Siralama</label>
                <select name="sort" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary bg-white">
                    <option value="">En Yeni</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>En Eski</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Ada Gore (A-Z)</option>
                    <option value="artpuan_desc" {{ request('sort') === 'artpuan_desc' ? 'selected' : '' }}>ArtPuan (Yuksek)</option>
                    <option value="artpuan_asc" {{ request('sort') === 'artpuan_asc' ? 'selected' : '' }}>ArtPuan (Dusuk)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700 transition">
                    Filtrele
                </button>
                @if(request()->hasAny(['search', 'role', 'has_orders', 'has_artpuan', 'sort']))
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">
                        Temizle
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Results Count -->
    <div class="flex items-center justify-between mb-3">
        <p class="text-sm text-gray-500">
            Toplam <span class="font-semibold text-gray-700">{{ $users->total() }}</span> kullanici
            @if($users->total() > 0)
                ({{ $users->firstItem() }}-{{ $users->lastItem() }} arasi gosteriliyor)
            @endif
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanici</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siparisler</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ArtPuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referanslar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kayit</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Islemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-400 font-mono">
                            #{{ $user->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->phone ?: '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 text-sm {{ $user->orders_count > 0 ? 'text-blue-700 font-medium' : 'text-gray-400' }}">
                                {{ $user->orders_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->art_puan > 0)
                                <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 font-medium">
                                    {{ number_format($user->art_puan, 2, ',', '.') }} AP
                                </span>
                            @else
                                <span class="text-sm text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->referrals_count }}
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_admin)
                                <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-800">Admin</span>
                            @else
                                <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">Kullanici</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ $user->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detay</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-gray-500 hover:text-gray-700 text-sm">Duzenle</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                @if(request()->hasAny(['search', 'role', 'has_orders', 'has_artpuan']))
                                    <p class="text-sm font-medium text-gray-500">Filtrelere uygun kullanici bulunamadi</p>
                                    <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mt-1 inline-block">Filtreleri temizle</a>
                                @else
                                    <p class="text-sm text-gray-500">Henuz kullanici yok.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
</x-admin.layouts.app>
