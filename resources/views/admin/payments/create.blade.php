<x-admin.layouts.app title="Yeni Ödeme Ekle">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Yeni Ödeme Ekle</h1>
        <a href="{{ route('admin.payments.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Ödemelere Dön
        </a>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.payments.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Sipariş Seçimi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sipariş *</label>
                    <select name="order_id" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="">Sipariş Seçin</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                {{ $order->order_number }} - {{ $order->customer_name }} - {{ number_format($order->total_tl, 0, ',', '.') }} TL
                            </option>
                        @endforeach
                    </select>
                    @error('order_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ödeme Yöntemi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Yöntemi *</label>
                    <select name="gateway" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="">Seçin</option>
                        <option value="havale" {{ old('gateway') === 'havale' ? 'selected' : '' }}>Havale / EFT</option>
                        <option value="garanti" {{ old('gateway') === 'garanti' ? 'selected' : '' }}>Garanti Kredi Kartı</option>
                        <option value="manual" {{ old('gateway') === 'manual' ? 'selected' : '' }}>Manuel Ödeme</option>
                    </select>
                    @error('gateway')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tutar --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tutar (TL) *</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    @error('amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Durum --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durum *</label>
                    <select name="status" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Başarılı</option>
                        <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Beklemede</option>
                        <option value="failed" {{ old('status') === 'failed' ? 'selected' : '' }}>Başarısız</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="border-gray-200">

                {{-- İşlem Bilgileri (Opsiyonel) --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">İşlem No</label>
                        <input type="text" name="transaction_id" value="{{ old('transaction_id') }}" placeholder="Otomatik oluşturulur" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Onay Kodu</label>
                        <input type="text" name="auth_code" value="{{ old('auth_code') }}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Referans No</label>
                        <input type="text" name="host_ref_num" value="{{ old('host_ref_num') }}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kart No (Maskelenmiş)</label>
                        <input type="text" name="card_number" value="{{ old('card_number') }}" placeholder="****1234" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>

                {{-- Notlar --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary" placeholder="Manuel ödeme eklenme sebebi...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                        Ödeme Ekle
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        İptal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin.layouts.app>
