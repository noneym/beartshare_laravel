<div class="min-h-[60vh] flex items-center justify-center py-12">
    <div class="w-full max-w-sm px-4">
        <div class="text-center mb-8">
            <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-brand-black100" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-semibold text-brand-black100">İki Adımlı Doğrulama</h1>
            <p class="text-gray-400 text-xs mt-2 leading-relaxed">
                @if($useRecovery)
                    Kurtarma kodlarınızdan birini girin.
                @elseif($method === 'totp')
                    Authenticator uygulamanızdaki 6 haneli kodu girin.
                @else
                    {{ $maskedPhone ? "+90 {$maskedPhone} numaralı telefonunuza" : 'Telefonunuza' }} gönderilen 6 haneli kodu girin.
                @endif
            </p>
        </div>

        @if(session('two_factor_notice'))
            <div class="mb-4 text-xs text-gray-600 bg-gray-50 border border-gray-200 px-3 py-2">{{ session('two_factor_notice') }}</div>
        @endif

        <form wire:submit="verify" class="space-y-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">{{ $useRecovery ? 'Kurtarma kodu' : 'Doğrulama kodu' }}</label>
                @if($useRecovery)
                    <input type="text" wire:model="code" autocomplete="off" autofocus placeholder="XXXX-XXXX"
                           class="w-full border px-4 py-2.5 text-sm font-mono uppercase tracking-wider focus:outline-none transition {{ $errors->has('code') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                @else
                    <input type="text" wire:model="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" autofocus placeholder="000000"
                           class="w-full border px-4 py-2.5 text-center text-lg font-mono tracking-[0.5em] focus:outline-none transition {{ $errors->has('code') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                @endif
                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait" class="w-full bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition">
                <span wire:loading.remove wire:target="verify">Doğrula ve Giriş Yap</span>
                <span wire:loading wire:target="verify">Doğrulanıyor...</span>
            </button>
        </form>

        <div class="flex items-center justify-between mt-6 text-xs">
            @if($method === 'sms' && !$useRecovery)
                <button type="button" wire:click="resend" class="text-gray-400 hover:text-brand-black100 transition">Kodu tekrar gönder</button>
            @else
                <span></span>
            @endif
            <button type="button" wire:click="toggleRecovery" class="text-gray-400 hover:text-brand-black100 transition">
                {{ $useRecovery ? 'Doğrulama kodu kullan' : 'Kurtarma kodu kullan' }}
            </button>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('login') }}" class="text-gray-400 text-xs hover:text-brand-black100">← Girişe dön</a>
        </div>
    </div>
</div>
