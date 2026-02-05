<x-admin.layouts.app title="Kullanici Detay - {{ $user->name }}">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-sm text-gray-500">Kullanici #{{ $user->id }}</p>
            </div>
            @if($user->is_admin)
                <span class="inline-block px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800 font-medium">Admin</span>
            @endif
        </div>
        <a href="{{ route('admin.users.edit', $user) }}" class="bg-primary hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium text-sm">
            Duzenle
        </a>
    </div>

    <!-- User Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Toplam Harcama</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($totalSpent, 0, ',', '.') }} TL</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Siparisler</p>
            <p class="text-xl font-bold text-gray-900">{{ $user->orders_count }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">ArtPuan Bakiye</p>
            <p class="text-xl font-bold text-green-600">{{ $user->formatted_art_puan }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Referanslar</p>
            <p class="text-xl font-bold text-gray-900">{{ $user->referrals_count }}</p>
        </div>
    </div>

    <!-- User Details + Referral Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Personal Info -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Kisisel Bilgiler</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-xs text-gray-500">Ad Soyad</dt>
                    <dd class="text-sm text-gray-900 font-medium">{{ $user->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">E-posta</dt>
                    <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Telefon</dt>
                    <dd class="text-sm text-gray-900">{{ $user->phone ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">TC Kimlik No</dt>
                    <dd class="text-sm text-gray-900 font-mono">{{ $user->tc_no ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Kayit Tarihi</dt>
                    <dd class="text-sm text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Referans Kodu</dt>
                    <dd class="text-sm text-gray-900 font-mono">{{ $user->referral_code }}</dd>
                </div>
            </dl>
        </div>

        <!-- Referral Info -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Referans Bilgileri</h2>

            @if($referrer)
                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-600 font-medium mb-1">Referans Eden</p>
                    <a href="{{ route('admin.users.show', $referrer) }}" class="text-sm text-blue-800 hover:text-blue-900 font-medium">
                        {{ $referrer->name }}
                    </a>
                    <p class="text-xs text-blue-500">{{ $referrer->email }}</p>
                </div>
            @else
                <p class="text-xs text-gray-400 mb-4">Referans eden yok</p>
            @endif

            <p class="text-xs text-gray-500 font-medium mb-2">Referans Ettikleri ({{ $user->referrals_count }})</p>
            @if($referrals->count() > 0)
                <div class="space-y-2">
                    @foreach($referrals as $ref)
                        <a href="{{ route('admin.users.show', $ref) }}" class="flex items-center justify-between p-2 rounded hover:bg-gray-50 transition">
                            <div>
                                <p class="text-sm text-gray-900">{{ $ref->name }}</p>
                                <p class="text-xs text-gray-500">{{ $ref->email }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $ref->created_at->format('d.m.Y') }}</span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400">Henuz referans yok</p>
            @endif
        </div>

        <!-- Favorites -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Favoriler ({{ $user->favorites_count }})</h2>
            @php
                $favArtworks = $user->favoriteArtworks()->with('artist')->take(8)->get();
            @endphp
            @if($favArtworks->count() > 0)
                <div class="space-y-2">
                    @foreach($favArtworks as $artwork)
                        <div class="flex items-center gap-3 p-2 rounded hover:bg-gray-50">
                            <div class="w-10 h-8 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                                @if($artwork->first_image)
                                    <img src="{{ $artwork->first_image_url }}" alt="{{ $artwork->title }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-900 truncate">{{ $artwork->title }}</p>
                                <p class="text-xs text-gray-500">{{ $artwork->artist->name ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400">Henuz favori yok</p>
            @endif
        </div>
    </div>

    <!-- Orders -->
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Siparisler</h2>
            @if($orders->count() > 0)
                <span class="text-xs text-gray-400">Son {{ $orders->count() }} siparis</span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase">Siparis No</th>
                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase">Odeme</th>
                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th class="px-5 py-2 text-right text-xs font-medium text-gray-500 uppercase">Islem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-mono text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-5 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($order->total_tl, 0, ',', '.') }} TL</p>
                                @if($order->artpuan_used > 0)
                                    <p class="text-xs text-green-600">-{{ number_format($order->artpuan_used, 2, ',', '.') }} AP</p>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">
                                {{ $order->payment_method === 'havale' ? 'Havale/EFT' : 'Kredi Karti' }}
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-blue-100 text-blue-800',
                                        'shipped' => 'bg-indigo-100 text-indigo-800',
                                        'delivered' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 text-xs">Goruntule</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-6 text-center text-sm text-gray-400">Siparis bulunamadi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ArtPuan Logs + Notification Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- ArtPuan Logs -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">ArtPuan Gecmisi</h2>
                <span class="text-xs text-gray-400">Son {{ $artPuanLogs->count() }} islem</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($artPuanLogs as $log)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-900">{{ $log->description ?: $log->type_label }}</p>
                            <p class="text-xs text-gray-400">{{ $log->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-mono font-medium {{ $log->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $log->amount >= 0 ? '+' : '' }}{{ number_format($log->amount, 2, ',', '.') }} AP
                            </p>
                            <p class="text-xs text-gray-400">Bakiye: {{ number_format($log->balance_after, 2, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-6 text-center text-sm text-gray-400">ArtPuan islemi yok</div>
                @endforelse
            </div>
        </div>

        <!-- Notification Logs -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Bildirim Gecmisi</h2>
                <span class="text-xs text-gray-400">Son {{ $notificationLogs->count() }} bildirim</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($notificationLogs as $nlog)
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="inline-block px-1.5 py-0.5 text-xs rounded {{ $nlog->channel === 'sms' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ strtoupper($nlog->channel) }}
                                </span>
                                <span class="inline-block px-1.5 py-0.5 text-xs rounded {{ $nlog->type_color }}">
                                    {{ $nlog->type_label }}
                                </span>
                            </div>
                            <span class="inline-block px-1.5 py-0.5 text-xs rounded {{ $nlog->status === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $nlog->status === 'success' ? 'Basarili' : 'Basarisiz' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $nlog->recipient }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $nlog->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                @empty
                    <div class="px-5 py-6 text-center text-sm text-gray-400">Bildirim bulunamadi</div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin.layouts.app>
