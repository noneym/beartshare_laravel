<div class="min-h-[60vh] flex items-center justify-center py-12">
    <div class="w-full max-w-sm px-4">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-brand-black100">Giriş Yap</h1>
            <p class="text-gray-400 text-xs mt-2">Hesabınıza giriş yapın</p>
        </div>

        @if(session('error'))
            <div class="mb-4 text-xs text-red-600 bg-red-50 border border-red-100 px-3 py-2">{{ session('error') }}</div>
        @endif

        <form wire:submit="login" class="space-y-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">E-posta</label>
                <input type="email" wire:model="email" class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}" placeholder="ornek@email.com">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Şifre</label>
                <x-password-input wire:model="password" class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}" placeholder="••••••••" />
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="remember" class="rounded border-gray-300 text-brand-black100 focus:ring-brand-black100">
                    <span class="ml-2 text-gray-400 text-xs">Beni hatırla</span>
                </label>
                <a href="#" class="text-xs text-gray-400 hover:text-brand-black100 transition link-underline pb-0.5">Şifremi unuttum</a>
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait" class="w-full bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition flex items-center justify-center">
                <svg wire:loading wire:target="login" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="login">Giriş Yap</span>
                <span wire:loading wire:target="login">Giriş yapılıyor...</span>
            </button>
        </form>

        @if(config('passkeys.enabled'))
            {{-- Şifresiz giriş: tarayıcı passkey desteklemiyorsa gizli kalır --}}
            <div wire:ignore x-data="{ ok: false, busy: false, error: '' }" x-init="ok = window.Passkey && Passkey.supported()" x-show="ok" x-cloak class="mt-4">
                <div class="flex items-center gap-3 my-4">
                    <span class="flex-1 h-px bg-gray-100"></span><span class="text-[11px] text-gray-400">veya</span><span class="flex-1 h-px bg-gray-100"></span>
                </div>
                <button type="button" :disabled="busy"
                        @click="busy = true; error = ''; Passkey.login().catch(e => { error = e.message; busy = false; })"
                        class="w-full border border-gray-200 hover:border-brand-black100 py-3 text-sm font-medium text-brand-black100 transition flex items-center justify-center gap-2 disabled:opacity-60">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                    </svg>
                    <span x-text="busy ? 'Bekleniyor…' : 'Passkey ile giriş yap'">Passkey ile giriş yap</span>
                </button>
                <p x-show="error" x-text="error" class="text-red-500 text-xs mt-2 text-center"></p>
                <p class="text-[11px] text-gray-400 mt-2 text-center">Face ID, parmak izi ya da cihaz PIN’i ile şifresiz giriş</p>
            </div>
            @include('partials.passkey-script')
        @endif

        <div class="text-center mt-8">
            <p class="text-gray-400 text-xs">
                Hesabınız yok mu?
                <a href="{{ route('register') }}" class="text-brand-black100 font-medium hover:underline">Kayıt Ol</a>
            </p>
        </div>
    </div>
</div>
