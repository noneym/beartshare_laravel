<x-admin.layouts.app title="Siparis Detayi">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Siparis #{{ $order->order_number }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Siparislere Don
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Update -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Siparis Durumu</h2>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="flex items-center gap-4">
                    @csrf
                    @method('PUT')
                    <select name="status" class="flex-1 rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Beklemede</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Onaylandi</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Kargoda</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Iptal Edildi</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                        Guncelle
                    </button>
                </form>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold">Siparis Urunleri</h2>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Urun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Birim Fiyat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($item->artwork && $item->artwork->image)
                                            <img src="{{ asset('storage/' . $item->artwork->image) }}" alt="{{ $item->artwork_title }}" class="w-16 h-16 object-cover rounded-lg mr-4">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $item->artwork_title }}</p>
                                            <p class="text-sm text-gray-500">{{ $item->artist_name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ number_format($item->price_tl, 0, ',', '.') }} TL</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ number_format($item->price_tl * $item->quantity, 0, ',', '.') }} TL</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-semibold">Toplam:</td>
                            <td class="px-6 py-4 font-bold text-lg text-primary">{{ number_format($order->total_tl, 0, ',', '.') }} TL</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Musteri Bilgileri</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500">Ad Soyad</dt>
                        <dd class="font-medium">{{ $order->customer_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">E-posta</dt>
                        <dd class="font-medium">{{ $order->customer_email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Telefon</dt>
                        <dd class="font-medium">{{ $order->customer_phone ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Teslimat Adresi</h2>
                <p class="text-gray-600">{{ $order->shipping_address ?? 'Adres bilgisi yok' }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Siparis Ozeti</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Siparis Tarihi</dt>
                        <dd class="font-medium">{{ $order->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Durum</dt>
                        <dd>
                            <span class="inline-block px-2 py-1 text-xs rounded-full
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ $order->status_label }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <dt class="text-gray-500">Toplam (TL)</dt>
                        <dd class="font-bold text-primary">{{ number_format($order->total_tl, 0, ',', '.') }} TL</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Toplam (USD)</dt>
                        <dd class="font-medium">{{ number_format($order->total_usd, 0, ',', '.') }} $</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-admin.layouts.app>
