<div class="min-h-[60vh] flex items-center justify-center py-12">
    <div class="w-full max-w-sm px-4">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-brand-black100">Yeni Şifre Belirle</h1>
            <p class="text-gray-400 text-xs mt-2">Hesabınız için yeni bir şifre belirleyin</p>
        </div>

        <form wire:submit="resetPassword" class="space-y-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">E-posta</label>
                <input type="email" wire:model="email" autocomplete="email" class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}" placeholder="ornek@email.com">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    <a href="{{ route('password.request') }}" class="inline-block text-xs text-brand-black100 font-medium hover:underline mt-1">Yeni bağlantı iste</a>
                @enderror
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Yeni şifre</label>
                <x-password-input wire:model="password" autocomplete="new-password" class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}" placeholder="En az 8 karakter" />
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Yeni şifre (tekrar)</label>
                <x-password-input wire:model="password_confirmation" autocomplete="new-password" class="w-full border border-gray-200 focus:border-brand-black100 px-4 py-2.5 text-sm focus:outline-none transition" placeholder="••••••••" />
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait" class="w-full bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition flex items-center justify-center">
                <svg wire:loading wire:target="resetPassword" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="resetPassword">Şifreyi Güncelle</span>
                <span wire:loading wire:target="resetPassword">Kaydediliyor...</span>
            </button>
        </form>
    </div>
</div>
