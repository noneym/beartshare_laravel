<x-admin.layouts.app title="Siparis Detayi">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Sipariş #{{ $order->order_number }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Siparişlere Dön
        </a>
    </div>

    {{-- Bildirim Mesajları --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Info -->
        <div class="lg:col-span-2 space-y-6">

            {{-- Sipariş Onaylama Kartı (Pending ise) --}}
            @if($order->status === 'pending')
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-amber-800 mb-1">Ödeme Bekleniyor</h3>
                        <p class="text-amber-700 text-sm mb-4">
                            Bu sipariş havale/EFT ödemesi beklemektedir.
                            @if($order->payment_code)
                                Müşterinin kullanması gereken açıklama kodu: <strong class="font-mono">{{ $order->payment_code }}</strong>
                            @endif
                        </p>
                        <div class="flex gap-3">
                            <form action="{{ route('admin.orders.update', $order) }}" method="POST" onsubmit="return confirm('Siparişi onaylamak istediğinizden emin misiniz? Bu işlem ArtPuan dağıtımı yapacak ve bildirimler gönderecektir.')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Siparişi Onayla
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.update', $order) }}" method="POST" onsubmit="return confirm('Siparişi iptal etmek istediğinizden emin misiniz?')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="px-6 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    İptal Et
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Status Update -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Sipariş Durumu</h2>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="flex items-center gap-4">
                    @csrf
                    @method('PUT')
                    <select name="status" class="flex-1 rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Ödeme Bekleniyor</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Onaylandı</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Kargoda</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                        Güncelle
                    </button>
                </form>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold">Sipariş Ürünleri</h2>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
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
                                        @if($item->artwork && $item->artwork->first_image_url)
                                            <img src="{{ $item->artwork->first_image_url }}" alt="{{ $item->artwork_title }}" class="w-16 h-16 object-cover rounded-lg mr-4">
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
                                            @if($item->artwork)
                                                @if($item->artwork->is_reserved)
                                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] bg-amber-100 text-amber-700 rounded">Rezerve</span>
                                                @elseif($item->artwork->is_sold)
                                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] bg-green-100 text-green-700 rounded">Satıldı</span>
                                                @endif
                                            @endif
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

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Customer Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Müşteri Bilgileri</h2>
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
                    @if($order->tc_no)
                    <div>
                        <dt class="text-sm text-gray-500">TC Kimlik No</dt>
                        <dd class="font-medium font-mono">{{ $order->tc_no }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Payment Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Ödeme Bilgileri</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500">Ödeme Yöntemi</dt>
                        <dd class="font-medium">{{ $order->payment_method_label }}</dd>
                    </div>
                    @if($order->payment_code)
                    <div>
                        <dt class="text-sm text-gray-500">Havale Kodu</dt>
                        <dd class="font-mono font-bold text-lg text-primary">{{ $order->payment_code }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Delivery Address -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Teslimat Adresi</h2>
                <p class="text-gray-600">{{ $order->shipping_address ?? 'Adres bilgisi yok' }}</p>
                @if($order->district || $order->city)
                    <p class="text-gray-600 mt-1">{{ $order->district }} / {{ $order->city }}</p>
                @endif
                @if($order->billing_address && $order->billing_address !== $order->shipping_address)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Fatura Adresi</h3>
                        <p class="text-gray-600">{{ $order->billing_address }}</p>
                    </div>
                @endif
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Sipariş Özeti</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Sipariş Tarihi</dt>
                        <dd class="font-medium">{{ $order->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @if($order->confirmed_at)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Onay Tarihi</dt>
                        <dd class="font-medium text-green-600">{{ $order->confirmed_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @endif
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
                    @if($order->artpuan_used > 0)
                    <div class="flex justify-between border-t pt-3">
                        <dt class="text-gray-500">ArtPuan Kullanıldı</dt>
                        <dd class="font-medium text-orange-600">{{ number_format($order->artpuan_used, 2, ',', '.') }} AP</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">ArtPuan İndirimi</dt>
                        <dd class="font-medium text-green-600">-{{ number_format($order->discount_tl, 0, ',', '.') }} TL</dd>
                    </div>
                    @endif
                    <div class="flex justify-between {{ $order->artpuan_used > 0 ? '' : 'border-t pt-3' }}">
                        <dt class="text-gray-500">{{ $order->artpuan_used > 0 ? 'Ödenecek Tutar' : 'Toplam (TL)' }}</dt>
                        <dd class="font-bold text-primary">{{ number_format($order->total_tl, 0, ',', '.') }} TL</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Toplam (USD)</dt>
                        <dd class="font-medium">{{ number_format($order->total_usd, 0, ',', '.') }} $</dd>
                    </div>
                </dl>
            </div>

            {{-- Notes --}}
            @if($order->notes)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Sipariş Notu</h2>
                <p class="text-gray-600 text-sm">{{ $order->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</x-admin.layouts.app>
