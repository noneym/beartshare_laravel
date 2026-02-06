<x-admin.layouts.app title="Siparisler">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Siparisler</h1>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siparis No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Musteri</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Islemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-900">{{ $order->customer_name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->customer_email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ number_format($order->total_tl, 0, ',', '.') }} TL</p>
                            <p class="text-sm text-gray-500">{{ number_format($order->total_usd, 0, ',', '.') }} $</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 text-xs rounded-full
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $order->status === 'payment_failed' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $order->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">Detay</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Henuz siparis yok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</x-admin.layouts.app>
