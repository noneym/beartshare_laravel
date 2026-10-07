<div class="min-h-[60vh] flex items-center justify-center py-12">
    <div class="w-full max-w-sm px-4">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-brand-black100">Şifremi Unuttum</h1>
            <p class="text-gray-400 text-xs mt-2 leading-relaxed">Hesabınıza kayıtlı e-posta adresini girin, şifre sıfırlama bağlantısı gönderelim.</p>
        </div>

        @if($sent)
            <div class="text-xs text-gray-600 bg-gray-50 border border-gray-200 px-4 py-3 leading-relaxed">
                Bu e-posta adresiyle kayıtlı bir hesap varsa şifre sıfırlama bağlantısı gönderildi. Gelen kutunuzu (ve gereksiz/spam klasörünü) kontrol edin. Bağlantı {{ config('auth.passwords.users.expire') }} dakika geçerlidir.
            </div>
            <button type="button" wire:click="$set('sent', false)" class="w-full mt-4 border border-gray-200 hover:border-brand-black100 py-3 text-sm font-medium text-brand-black100 transition">
                Tekrar gönder
            </button>
        @else
            <form wire:submit="sendLink" class="space-y-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">E-posta</label>
                    <input type="email" wire:model="email" autofocus autocomplete="email" class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}" placeholder="ornek@email.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait" class="w-full bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition flex items-center justify-center">
                    <svg wire:loading wire:target="sendLink" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="sendLink">Sıfırlama Bağlantısı Gönder</span>
                    <span wire:loading wire:target="sendLink">Gönderiliyor...</span>
                </button>
            </form>
        @endif

        <div class="text-center mt-8">
            <a href="{{ route('login') }}" class="text-gray-400 text-xs hover:text-brand-black100 transition link-underline pb-0.5">Giriş sayfasına dön</a>
        </div>
    </div>
</div>
