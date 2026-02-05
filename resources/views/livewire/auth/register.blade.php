<div class="min-h-[60vh] flex items-center justify-center py-12">
    <div class="w-full max-w-sm px-4">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-light text-brand-black100">Kayıt <span class="font-semibold">Ol</span></h1>
            <p class="text-gray-400 text-xs mt-2">BeArtShare ailesine katılın</p>
        </div>

        <form wire:submit="register" class="space-y-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Ad Soyad</label>
                <input type="text" wire:model="name" class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}" placeholder="Adınız Soyadınız">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1.5">E-posta</label>
                <input type="email" wire:model="email" class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}" placeholder="ornek@email.com">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Şifre</label>
                <input type="password" wire:model="password" class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}" placeholder="••••••••">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Şifre Tekrar</label>
                <input type="password" wire:model="password_confirmation" class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition" placeholder="••••••••">
            </div>

            <div>
                <label class="flex items-start">
                    <input type="checkbox" wire:model="terms" class="rounded border-gray-300 text-brand-black100 focus:ring-brand-black100 mt-0.5">
                    <span class="ml-2 text-gray-400 text-xs leading-relaxed">
                        <a href="#" class="text-brand-black100 hover:underline">Kullanım şartlarını</a> ve
                        <a href="#" class="text-brand-black100 hover:underline">gizlilik politikasını</a> okudum, kabul ediyorum.
                    </span>
                </label>
                @error('terms') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait" class="w-full bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition flex items-center justify-center">
                <svg wire:loading wire:target="register" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="register">Kayıt Ol</span>
                <span wire:loading wire:target="register">Kayıt yapılıyor...</span>
            </button>
        </form>

        <div class="text-center mt-8">
            <p class="text-gray-400 text-xs">
                Zaten hesabınız var mı?
                <a href="{{ route('login') }}" class="text-brand-black100 font-medium hover:underline">Giriş Yap</a>
            </p>
        </div>
    </div>
</div>
