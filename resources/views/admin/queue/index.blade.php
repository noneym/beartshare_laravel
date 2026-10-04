<x-admin.layouts.app>
    <x-slot name="title">Kuyruk</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kuyruk</h1>
            <p class="text-sm text-gray-500 mt-1">Toplu SMS / e-posta gönderimleri ve arka plan işleri</p>
        </div>
        <a href="{{ route('admin.queue.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Yenile</a>
    </div>

    {{-- Durum kartları --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Worker</p>
            @if($stats['worker_alive'])
                <p class="text-lg font-bold text-green-600 mt-1">Çalışıyor</p>
            @else
                <p class="text-lg font-bold text-red-600 mt-1">Durmuş</p>
            @endif
            <p class="text-xs text-gray-400 mt-1">
                {{ $stats['heartbeat'] ? 'Son sinyal ' . \Carbon\Carbon::createFromTimestamp($stats['heartbeat'])->diffForHumans() : 'Sinyal yok' }}
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Bekleyen</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">İşleniyor</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['running']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Hatalı</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['failed']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">USD/TRY</p>
            @php $lastRate = $rates->first(); @endphp
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $lastRate ? number_format($lastRate->rate, 4, ',', '.') : '—' }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $lastRate ? $lastRate->created_at->format('d.m.Y H:i') : 'Henüz çekilmedi' }}</p>
        </div>
    </div>

    @if(!$stats['worker_alive'] && $stats['pending'] > 0)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg px-4 py-3 mb-6">
            Bekleyen iş var ama worker sinyal vermiyor. Sunucuda <code class="bg-amber-100 px-1">php artisan queue:work</code> sürecinin çalıştığını kontrol edin.
        </div>
    @endif

    {{-- Toplu gönderimler --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 py-3 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Toplu Gönderimler</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Gönderim</th>
                        <th class="px-4 py-2 text-left w-64">İlerleme</th>
                        <th class="px-4 py-2 text-right">Hatalı</th>
                        <th class="px-4 py-2 text-left">Başlangıç</th>
                        <th class="px-4 py-2 text-left">Durum</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($batches as $b)
                        <tr class="{{ $highlight === $b->id ? 'bg-primary/5' : '' }}">
                            <td class="px-4 py-2 text-gray-800">{{ $b->name }}</td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 {{ $b->failed_jobs ? 'bg-amber-500' : 'bg-green-500' }}" style="width: {{ $b->progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $b->processed }} / {{ $b->total_jobs }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right {{ $b->failed_jobs ? 'text-red-600 font-medium' : 'text-gray-400' }}">{{ $b->failed_jobs }}</td>
                            <td class="px-4 py-2 text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::createFromTimestamp($b->created_at)->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @if($b->cancelled_at)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">İptal edildi</span>
                                @elseif($b->running)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">Gönderiliyor</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Tamamlandı</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                @if($b->running)
                                    <form method="POST" action="{{ route('admin.queue.batches.cancel', $b->id) }}" onsubmit="return confirm('Kalan gönderimler iptal edilsin mi?')">
                                        @csrf
                                        <button class="text-xs text-red-600 hover:underline">İptal et</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Henüz toplu gönderim yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Bekleyen işler --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Bekleyen İşler <span class="text-xs text-gray-400 font-normal">(ilk 50)</span></h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @forelse($jobs as $j)
                            <tr>
                                <td class="px-4 py-2 text-gray-400">#{{ $j->id }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $j->name }}</td>
                                <td class="px-4 py-2 text-xs">
                                    @if($j->reserved_at)
                                        <span class="text-blue-600">işleniyor</span>
                                    @else
                                        <span class="text-gray-500">sırada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-400 text-right">{{ \Carbon\Carbon::createFromTimestamp($j->created_at)->format('d.m H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-6 text-center text-gray-400">Kuyruk boş.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Kur geçmişi --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Döviz Kuru (TCMB)</h2>
                <form method="POST" action="{{ route('admin.queue.rates') }}">
                    @csrf
                    <button class="text-xs px-3 py-1.5 bg-primary text-white rounded hover:opacity-90">Şimdi Güncelle</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Zaman</th>
                            <th class="px-4 py-2 text-right">USD/TRY</th>
                            <th class="px-4 py-2 text-right">Güncellenen eser</th>
                            <th class="px-4 py-2 text-left">Kaynak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rates as $r)
                            <tr>
                                <td class="px-4 py-2 text-gray-500">{{ $r->created_at->format('d.m.Y H:i') }}</td>
                                <td class="px-4 py-2 text-right font-medium">{{ number_format($r->rate, 4, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right text-gray-500">{{ $r->artworks_updated }}</td>
                                <td class="px-4 py-2 text-xs text-gray-500">{{ $r->source }}{{ $r->rate_date ? ' · ' . $r->rate_date->format('d.m.Y') : '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Henüz kur çekilmedi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Hatalı işler --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Hatalı İşler</h2>
            @if($failed->isNotEmpty())
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.queue.retry') }}">
                        @csrf
                        <input type="hidden" name="uuid" value="all">
                        <button class="text-xs px-3 py-1.5 border border-gray-300 rounded hover:bg-gray-50">Tümünü tekrar dene</button>
                    </form>
                    <form method="POST" action="{{ route('admin.queue.forget') }}" onsubmit="return confirm('Tüm hatalı iş kayıtları silinsin mi?')">
                        @csrf
                        <input type="hidden" name="uuid" value="all">
                        <button class="text-xs px-3 py-1.5 border border-red-200 text-red-600 rounded hover:bg-red-50">Tümünü sil</button>
                    </form>
                </div>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    @forelse($failed as $f)
                        <tr>
                            <td class="px-4 py-2 text-gray-800 whitespace-nowrap">{{ $f->name }}</td>
                            <td class="px-4 py-2 text-xs text-red-600">{{ \Illuminate\Support\Str::limit($f->error, 160) }}</td>
                            <td class="px-4 py-2 text-xs text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($f->failed_at)->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.queue.retry') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="uuid" value="{{ $f->uuid }}">
                                    <button class="text-xs text-primary hover:underline">Tekrar dene</button>
                                </form>
                                <form method="POST" action="{{ route('admin.queue.forget') }}" class="inline ml-2">
                                    @csrf
                                    <input type="hidden" name="uuid" value="{{ $f->uuid }}">
                                    <button class="text-xs text-gray-500 hover:underline">Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-center text-gray-400">Hatalı iş yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="px-4 py-2 text-xs text-gray-400 border-t border-gray-100">Gönderim ayrıntıları (alıcı, hata mesajı) Bildirim Log sayfasında.</p>
    </div>

    @if($batches->contains('running', true) || $stats['pending'] > 0)
        {{-- Gönderim sürerken sayfayı kendiliğinden yenile --}}
        <script>setTimeout(() => location.reload(), 5000);</script>
    @endif
</x-admin.layouts.app>
