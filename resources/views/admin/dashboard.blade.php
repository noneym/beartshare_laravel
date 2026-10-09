<x-admin.layouts.app title="Dashboard">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Dashboard</h1>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Sanatcilar</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['artists'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Eserler</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['artworks'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Siparisler</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['orders'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Toplam Satis</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_sales'], 0, ',', '.') }} TL</p>
                    <p class="text-xs text-gray-500 mt-0.5" title="Her satış, satış tarihindeki dolar kuruyla">{{ number_format($stats['total_sales_usd'], 0, ',', '.') }} $ <span class="text-gray-400">(satış tarihi kuruyla)</span></p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Aylık satış -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Aylık Satış</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ number_format($monthly['total'], 0, ',', '.') }} TL · {{ $monthly['orders'] }} sipariş
                    <span class="text-gray-400">(ödemesi alınmış siparişler, ödeme tarihine göre)</span>
                </p>
            </div>
            <div class="inline-flex rounded-lg border border-gray-200 overflow-hidden text-sm">
                @foreach(['12' => 'Son 12 ay', '24' => 'Son 24 ay', 'tumu' => 'Tümü'] as $key => $label)
                    <a href="{{ route('admin.dashboard', ['ay' => $key]) }}"
                       class="px-3 py-1.5 {{ $range === $key ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50' }} {{ !$loop->first ? 'border-l border-gray-200' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
        <div class="relative h-72">
            <canvas id="monthlySalesChart"></canvas>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        (function () {
            const data = @js($monthly);
            const tl = (v) => new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 }).format(v) + ' TL';
            new Chart(document.getElementById('monthlySalesChart'), {
                data: {
                    labels: data.labels,
                    datasets: [
                        { type: 'bar', label: 'Ciro (TL)', data: data.revenue, backgroundColor: 'rgba(212, 160, 23, 0.75)', borderRadius: 4, yAxisID: 'y' },
                        { type: 'line', label: 'Sipariş', data: data.count, borderColor: '#111827', backgroundColor: '#111827', tension: 0.3, cubicInterpolationMode: 'monotone', pointRadius: 3, yAxisID: 'y1' },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: (c) => c.dataset.yAxisID === 'y' ? ' Ciro: ' + tl(c.parsed.y) : ' Sipariş: ' + c.parsed.y } },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: (v) => v >= 1e6 ? (v / 1e6).toLocaleString('tr-TR') + ' M' : (v >= 1e3 ? (v / 1e3).toLocaleString('tr-TR') + ' B' : v) } },
                        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } },
                    },
                },
            });
        })();
    </script>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Artworks -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Son Eklenen Eserler</h2>
            </div>
            <div class="p-6">
                @forelse($recentArtworks as $artwork)
                    <div class="flex items-center gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            @if($artwork->first_image)
                                <img src="{{ $artwork->first_image_url }}" alt="{{ $artwork->title }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $artwork->title }}</p>
                            <p class="text-sm text-gray-500">{{ $artwork->artist->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ $artwork->formatted_price_tl }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Henuz eser eklenmemis.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Son Siparisler</h2>
            </div>
            <div class="p-6">
                @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div>
                            <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                            <p class="text-sm text-gray-500">{{ $order->customer_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ number_format($order->total_tl, 0, ',', '.') }} TL</p>
                            <span class="inline-block px-2 py-1 text-xs rounded-full {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Henuz siparis yok.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin.layouts.app>
