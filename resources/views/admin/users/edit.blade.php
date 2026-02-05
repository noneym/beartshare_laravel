<x-admin.layouts.app title="Kullanici Duzenle - {{ $user->name }}">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.users.show', $user) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kullanici Duzenle</h1>
            <p class="text-sm text-gray-500">{{ $user->name }} - #{{ $user->id }}</p>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider pb-3 border-b border-gray-100">Kisisel Bilgiler</h2>

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-medium text-gray-700 mb-1">Ad Soyad <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-medium text-gray-700 mb-1">E-posta <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-xs font-medium text-gray-700 mb-1">Telefon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- TC No -->
            <div>
                <label for="tc_no" class="block text-xs font-medium text-gray-700 mb-1">TC Kimlik No</label>
                <input type="text" name="tc_no" id="tc_no" value="{{ old('tc_no', $user->tc_no) }}" maxlength="11"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-primary @error('tc_no') border-red-500 @enderror">
                @error('tc_no')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 space-y-5 mt-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider pb-3 border-b border-gray-100">Yetki & ArtPuan</h2>

            <!-- Is Admin -->
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_admin" value="0">
                <input type="checkbox" name="is_admin" id="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                       class="w-4 h-4 border-gray-300 rounded text-primary focus:ring-primary">
                <label for="is_admin" class="text-sm text-gray-700">Admin Yetkisi</label>
            </div>

            <!-- ArtPuan -->
            <div>
                <label for="art_puan" class="block text-xs font-medium text-gray-700 mb-1">ArtPuan Bakiye</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="art_puan" id="art_puan" value="{{ old('art_puan', $user->art_puan) }}" step="0.01" min="0"
                           class="w-48 border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-primary @error('art_puan') border-red-500 @enderror">
                    <span class="text-sm text-gray-500">AP</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Degistirirseniz otomatik olarak ArtPuan loguna kaydedilir.</p>
                @error('art_puan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Read-only Info -->
        <div class="bg-gray-50 rounded-xl p-6 mt-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider pb-3 border-b border-gray-200 mb-4">Salt Okunur Bilgiler</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-gray-500">Kayit Tarihi</span>
                    <p class="text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Referans Kodu</span>
                    <p class="text-gray-900 font-mono">{{ $user->referral_code }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Referans Eden</span>
                    <p class="text-gray-900">
                        @if($user->referrer)
                            <a href="{{ route('admin.users.show', $user->referrer) }}" class="text-blue-600 hover:text-blue-800">{{ $user->referrer->name }}</a>
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Referans Edilen Kisi Sayisi</span>
                    <p class="text-gray-900">{{ $user->referrals()->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="bg-gray-800 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                Kaydet
            </button>
            <a href="{{ route('admin.users.show', $user) }}" class="text-gray-500 hover:text-gray-700 text-sm">
                Iptal
            </a>
        </div>
    </form>
</x-admin.layouts.app>
