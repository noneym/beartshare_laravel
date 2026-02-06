<x-admin.layouts.app title="Manuel ArtPuan Ekle">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manuel ArtPuan Ekle</h1>
            <p class="text-sm text-gray-500 mt-1">Kullaniciya manuel olarak ArtPuan ekleyin</p>
        </div>
        <a href="{{ route('admin.art-puan-logs.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; ArtPuan Log'a Don
        </a>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('admin.art-puan-logs.store') }}" method="POST" class="bg-white rounded-xl shadow-sm p-6 space-y-6">
            @csrf

            {{-- Kullanici Secimi --}}
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Kullanici *</label>
                <select name="user_id" id="user_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">Kullanici Secin</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }}) - Mevcut: {{ number_format($user->art_puan, 2, ',', '.') }} AP
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Miktar --}}
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Miktar (AP) *</label>
                <input type="number" step="0.01" min="0.01" name="amount" id="amount" value="{{ old('amount') }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                       placeholder="Ornek: 10.00">
                @error('amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tip --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tip *</label>
                <select name="type" id="type" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="bonus" {{ old('type') === 'bonus' ? 'selected' : '' }}>Bonus</option>
                    <option value="manual" {{ old('type') === 'manual' ? 'selected' : '' }}>Manuel</option>
                    <option value="refund" {{ old('type') === 'refund' ? 'selected' : '' }}>Iade</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Aciklama --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Aciklama *</label>
                <textarea name="description" id="description" rows="3" required
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                          placeholder="ArtPuan ekleme sebebini yazin...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bildirim Secenekleri --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm font-medium text-blue-800 mb-3">Kullaniciyi Bilgilendir</p>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="notify_sms" value="1" {{ old('notify_sms') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            SMS ile bilgilendir
                        </span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="notify_email" value="1" {{ old('notify_email') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            E-posta ile bilgilendir
                        </span>
                    </label>
                </div>
                <p class="text-xs text-blue-600 mt-2">Secili kanallarda kullaniciya ArtPuan eklendigi bildirilecektir.</p>
            </div>

            {{-- Uyari --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-sm text-yellow-700">
                        Bu islem kullanicinin ArtPuan bakiyesini aninda arttiracaktir. Lutfen bilgileri dikkatli kontrol edin.
                    </p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.art-puan-logs.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Iptal
                </a>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                    ArtPuan Ekle
                </button>
            </div>
        </form>
    </div>
</x-admin.layouts.app>
