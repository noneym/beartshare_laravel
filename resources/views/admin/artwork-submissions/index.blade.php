<x-admin.layouts.app>
    <x-slot name="title">Eser Başvuruları</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Eser Başvuruları</h1>
            <p class="text-sm text-gray-500 mt-1">"Eser Kabulü" formundan gelen başvurular</p>
        </div>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Toplam</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Yeni</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['new']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">İnceleniyor</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['reviewing']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Kabul</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['accepted']) }}</p>
        </div>
    </div>

    {{-- Filtreler --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.artwork-submissions.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="İsim, e-posta, sanatçı, eser..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div class="min-w-[150px]">
                <label class="block text-xs text-gray-500 mb-1">Durum</label>
                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">Tümü</option>
                    @foreach(\App\Models\ArtworkSubmission::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700 transition">
                Filtrele
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.artwork-submissions.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Başvuran</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sanatçı / Eser</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiyat Beklentisi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($submissions as $sub)
                    <tr class="hover:bg-gray-50 {{ $sub->status === 'new' ? 'bg-yellow-50' : '' }}">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                            {{ $sub->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $sub->name }}</p>
                            <p class="text-xs text-gray-400">{{ $sub->email }}</p>
                            <p class="text-xs text-gray-400">{{ $sub->phone }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $sub->artist_name }}</p>
                            <p class="text-xs text-gray-500">{{ $sub->artwork_title }}</p>
                            @if($sub->technique || $sub->year)
                                <p class="text-[10px] text-gray-400">{{ $sub->technique }} @if($sub->year) · {{ $sub->year }} @endif</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                            {{ $sub->expected_price ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                            {{ is_array($sub->images) ? count($sub->images) : 0 }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $colors = [
                                    'new'       => 'bg-yellow-100 text-yellow-800',
                                    'reviewing' => 'bg-blue-100 text-blue-800',
                                    'accepted'  => 'bg-green-100 text-green-800',
                                    'rejected'  => 'bg-red-100 text-red-800',
                                    'closed'    => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colors[$sub->status] ?? 'bg-gray-100' }}">
                                {{ $sub->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs">
                            <a href="{{ route('admin.artwork-submissions.show', $sub) }}" class="text-primary hover:underline font-medium">
                                Detay
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">
                            Henüz başvuru bulunmuyor.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($submissions->hasPages())
        <div class="mt-4">{{ $submissions->links() }}</div>
    @endif
</x-admin.layouts.app>
