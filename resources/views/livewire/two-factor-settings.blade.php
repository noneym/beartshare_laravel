<div class="bg-white border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider">İki Adımlı Doğrulama</h3>
        @if($user->hasTwoFactor())
            <span class="text-[11px] px-2 py-0.5 bg-green-50 text-green-700 border border-green-100">Açık · {{ $user->two_factor_label }}</span>
        @else
            <span class="text-[11px] px-2 py-0.5 bg-gray-50 text-gray-500 border border-gray-200">Kapalı</span>
        @endif
    </div>

    @if(session('two_factor_status'))
        <div class="mb-4 text-xs text-gray-700 bg-gray-50 border border-gray-200 px-3 py-2">{{ session('two_factor_status') }}</div>
    @endif

    {{-- Kurtarma kodları (yalnızca bir kez gösterilir) --}}
    @if($recoveryCodes)
        <div class="mb-5 border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs text-amber-800 font-medium mb-1">Kurtarma kodlarınızı güvenli bir yere kaydedin</p>
            <p class="text-xs text-amber-700 mb-3">Telefonunuza erişemezseniz bu kodlardan biriyle giriş yapabilirsiniz. Her kod bir kez kullanılır ve bu kodlar bir daha gösterilmez.</p>
            <div class="grid grid-cols-2 gap-2 font-mono text-sm text-brand-black100 bg-white border border-amber-100 p-3" x-data x-ref="codes">
                @foreach($recoveryCodes as $rc)
                    <span>{{ $rc }}</span>
                @endforeach
            </div>
            <div class="flex gap-4 mt-3 text-xs">
                <button type="button" x-data="{ copied: false }" @click="navigator.clipboard.writeText(@js(implode("\n", $recoveryCodes))); copied = true; setTimeout(() => copied = false, 2000)"
                        class="text-amber-800 hover:underline" x-text="copied ? 'Kopyalandı' : 'Kodları kopyala'">Kodları kopyala</button>
                <button type="button" wire:click="hideCodes" class="text-amber-800 hover:underline">Kaydettim, gizle</button>
            </div>
        </div>
    @endif

    @if(!$user->hasTwoFactor())
        @if($step === null)
            <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                Açıldığında girişte şifrenize ek olarak bir doğrulama kodu istenir. Şifreniz başkasının eline geçse bile hesabınıza erişilemez.
            </p>
            <div class="flex flex-wrap gap-3">
                <button wire:click="startSms" wire:loading.attr="disabled" @disabled(!$user->phone)
                        class="border border-gray-200 px-4 py-2.5 text-sm text-brand-black100 hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    SMS ile aç
                </button>
                <button wire:click="startTotp" class="border border-gray-200 px-4 py-2.5 text-sm text-brand-black100 hover:bg-gray-50 transition">
                    Authenticator uygulaması ile aç
                </button>
            </div>
            @unless($user->phone)
                <p class="text-xs text-gray-400 mt-2">SMS için hesabınızda kayıtlı telefon numarası gerekir.</p>
            @endunless
            @error('code') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
        @else
            @if($step === 'sms')
                <p class="text-xs text-gray-500 mb-4">+90 {{ substr($user->phone, 0, 3) }} *** ** {{ substr($user->phone, -2) }} numarasına 6 haneli bir kod gönderdik. Açmak için kodu girin.</p>
            @elseif($step === 'totp')
                <div class="flex flex-col sm:flex-row gap-5 mb-4">
                    <div class="w-44 h-44 shrink-0 border border-gray-100 p-2 bg-white [&_svg]:w-full [&_svg]:h-full">{!! $qrSvg !!}</div>
                    <div class="text-xs text-gray-500 leading-relaxed">
                        <p class="mb-2">1. Google Authenticator, Microsoft Authenticator gibi bir uygulamayla QR kodu okutun.</p>
                        <p class="mb-2">QR okutamıyorsanız bu anahtarı elle girin:</p>
                        <p class="font-mono text-sm text-brand-black100 bg-gray-50 border border-gray-100 px-2 py-1.5 break-all select-all">{{ $secret }}</p>
                        <p class="mt-2">2. Uygulamanın gösterdiği 6 haneli kodu aşağıya girin.</p>
                    </div>
                </div>
            @endif

            <form wire:submit="confirm" class="flex flex-wrap items-start gap-3">
                <div>
                    <input type="text" wire:model="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000"
                           class="w-36 border px-4 py-2.5 text-center font-mono tracking-[0.3em] text-sm focus:outline-none transition {{ $errors->has('code') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="bg-brand-black100 hover:bg-black text-white px-5 py-2.5 text-sm font-medium transition">Doğrula ve Aç</button>
                <button type="button" wire:click="cancel" class="border border-gray-200 px-5 py-2.5 text-sm text-gray-500 hover:bg-gray-50 transition">İptal</button>
            </form>
            @if($step === 'sms')
                <button type="button" wire:click="resendSms" class="text-xs text-gray-400 hover:text-brand-black100 mt-3">Kodu tekrar gönder</button>
            @endif
        @endif
    @else
        @if($step === null)
            <p class="text-xs text-gray-500 mb-1">
                Girişte {{ $user->two_factor_method === 'sms' ? 'telefonunuza gönderilen SMS kodu' : 'Authenticator uygulamanızdaki kod' }} isteniyor.
            </p>
            <p class="text-xs text-gray-400 mb-4">Kullanılmamış kurtarma kodu: {{ $remainingCodes }}</p>
            <div class="flex flex-wrap gap-4 text-xs">
                <button wire:click="askPassword('regenerate')" class="text-primary hover:underline">Kurtarma kodlarını yenile</button>
                <button wire:click="askPassword('disable')" class="text-red-600 hover:underline">İki adımlı doğrulamayı kapat</button>
            </div>
        @else
            <p class="text-xs text-gray-500 mb-3">
                {{ $step === 'disable' ? 'Kapatmak için' : 'Yeni kurtarma kodları için' }} mevcut şifrenizi girin.
            </p>
            <form wire:submit="{{ $step === 'disable' ? 'disable' : 'regenerate' }}" class="flex flex-wrap items-start gap-3">
                <div class="w-60">
                    <x-password-input wire:model="password" autocomplete="current-password"
                           class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}" />
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="{{ $step === 'disable' ? 'bg-red-600 hover:bg-red-700' : 'bg-brand-black100 hover:bg-black' }} text-white px-5 py-2.5 text-sm font-medium transition">
                    {{ $step === 'disable' ? 'Kapat' : 'Yenile' }}
                </button>
                <button type="button" wire:click="cancel" class="border border-gray-200 px-5 py-2.5 text-sm text-gray-500 hover:bg-gray-50 transition">İptal</button>
            </form>
        @endif
    @endif
</div>
