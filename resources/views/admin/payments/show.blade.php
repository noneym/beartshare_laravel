<x-admin.layouts.app title="Ödeme Detayı">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Ödeme #{{ $payment->id }}</h1>
        <a href="{{ route('admin.payments.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Ödemelere Dön
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Sol Kolon --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- İşlem Bilgileri --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">İşlem Bilgileri</h2>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">İşlem ID</dt>
                        <dd class="font-mono text-sm">{{ $payment->transaction_id ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Gateway İşlem ID</dt>
                        <dd class="font-mono text-sm">{{ $payment->gateway_transaction_id ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Onay Kodu</dt>
                        <dd class="font-mono font-bold text-lg text-green-600">{{ $payment->auth_code ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Host Referans No</dt>
                        <dd class="font-mono text-sm">{{ $payment->host_ref_num ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Kart Numarası</dt>
                        <dd class="font-mono">{{ $payment->card_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Taksit</dt>
                        <dd>{{ $payment->installment_count ? $payment->installment_count . ' Taksit' : 'Tek Çekim' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Hata Bilgisi --}}
            @if($payment->error_code || $payment->error_message)
            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-red-800 mb-4">Hata Bilgisi</h2>
                <dl class="space-y-2">
                    @if($payment->error_code)
                    <div>
                        <dt class="text-sm text-red-600">Hata Kodu</dt>
                        <dd class="font-mono text-red-800">{{ $payment->error_code }}</dd>
                    </div>
                    @endif
                    @if($payment->error_message)
                    <div>
                        <dt class="text-sm text-red-600">Hata Mesajı</dt>
                        <dd class="text-red-800">{{ $payment->error_message }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            {{-- Request Data --}}
            @if($payment->request_data)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">İstek Verisi</h2>
                <pre class="bg-gray-50 rounded-lg p-4 text-xs overflow-x-auto">{{ json_encode($payment->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            {{-- Response Data --}}
            @if($payment->response_data)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Yanıt Verisi</h2>
                <pre class="bg-gray-50 rounded-lg p-4 text-xs overflow-x-auto">{{ json_encode($payment->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
        </div>

        {{-- Sağ Kolon --}}
        <div class="space-y-6">
            {{-- Özet --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Özet</h2>
                <dl class="space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b">
                        <dt class="text-gray-500">Tutar</dt>
                        <dd class="text-2xl font-bold text-primary">{{ $payment->formatted_amount }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Durum</dt>
                        <dd>
                            <span class="inline-block px-3 py-1 text-sm rounded-full
                                {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $payment->status === 'refunded' ? 'bg-blue-100 text-blue-800' : '' }}
                            ">
                                {{ $payment->status_text }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Ödeme Yöntemi</dt>
                        <dd class="font-medium">{{ ucfirst($payment->gateway) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Para Birimi</dt>
                        <dd>{{ $payment->currency }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tarih</dt>
                        <dd>{{ $payment->created_at->format('d.m.Y H:i:s') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Sipariş Bilgisi --}}
            @if($payment->order)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Sipariş</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500">Sipariş No</dt>
                        <dd>
                            <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $payment->order->order_number }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Müşteri</dt>
                        <dd class="font-medium">{{ $payment->order->customer_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">E-posta</dt>
                        <dd>{{ $payment->order->customer_email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Sipariş Durumu</dt>
                        <dd>
                            <span class="inline-block px-2 py-1 text-xs rounded-full
                                {{ $payment->order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $payment->order->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $payment->order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                            ">
                                {{ $payment->order->status_label }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            {{-- İşlemler --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">İşlemler</h2>
                <div class="space-y-2">
                    @if(!$payment->trashed())
                        <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('Bu ödeme kaydını silmek istediğinize emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm">
                                Ödeme Kaydını Sil
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.payments.restore', $payment->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 text-sm">
                                Geri Yükle
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin.layouts.app>
