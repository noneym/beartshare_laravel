<div class="min-h-[60vh] flex items-center justify-center py-12"
     x-data="{
        countdown: @entangle('resend_cooldown'),
        timer: null,
        startCountdown() {
            this.countdown = 120;
            clearInterval(this.timer);
            this.timer = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--;
                } else {
                    clearInterval(this.timer);
                }
            }, 1000);
        },
        formatTime(s) {
            return Math.floor(s/60) + ':' + String(s%60).padStart(2,'0');
        },
        showResent: false
     }"
     x-on:start-countdown.window="startCountdown()"
     x-on:code-resent.window="showResent = true; setTimeout(() => showResent = false, 3000)"
>
    <div class="w-full max-w-sm px-4">

        {{-- Step Indicator --}}
        <div class="flex items-center justify-center mb-8 gap-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold {{ $step === 1 ? 'bg-brand-black100 text-white' : 'bg-green-500 text-white' }}">
                    @if($step > 1)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    @else
                        1
                    @endif
                </div>
                <span class="text-xs {{ $step === 1 ? 'text-brand-black100 font-medium' : 'text-gray-400' }}">Bilgiler</span>
            </div>
            <div class="w-8 h-px bg-gray-300"></div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold {{ $step === 2 ? 'bg-brand-black100 text-white' : 'bg-gray-200 text-gray-400' }}">
                    2
                </div>
                <span class="text-xs {{ $step === 2 ? 'text-brand-black100 font-medium' : 'text-gray-400' }}">Doğrulama</span>
            </div>
        </div>

        {{-- STEP 1: Registration Form --}}
        @if($step === 1)
        <div>
            <div class="text-center mb-8">
                <h1 class="text-2xl font-light text-brand-black100">Kayıt <span class="font-semibold">Ol</span></h1>
                <p class="text-gray-400 text-xs mt-2">BeArtShare ailesine katılın</p>
            </div>

            <form wire:submit="sendVerification" class="space-y-4">
                {{-- Ad Soyad --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Ad Soyad</label>
                    <input type="text" wire:model="name"
                           class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                           placeholder="Adınız Soyadınız">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- TC Kimlik No --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">TC Kimlik No</label>
                    <input type="text" wire:model="tc_no" maxlength="11" inputmode="numeric"
                           class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('tc_no') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                           placeholder="XXXXXXXXXXX">
                    @error('tc_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Cep Telefonu --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Cep Telefonu</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm">
                            +90
                        </span>
                        <input type="tel" wire:model="phone" maxlength="11" inputmode="tel"
                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('phone') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                               placeholder="5XX XXX XX XX">
                    </div>
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- E-posta --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">E-posta</label>
                    <input type="email" wire:model="email"
                           class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                           placeholder="ornek@email.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Şifre --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Şifre</label>
                    <input type="password" wire:model="password"
                           class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                           placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Şifre Tekrar --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Şifre Tekrar</label>
                    <input type="password" wire:model="password_confirmation"
                           class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition"
                           placeholder="••••••••">
                </div>

                {{-- Referans Kodu --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">
                        Referans Kodu
                        <span class="text-gray-300">(opsiyonel)</span>
                    </label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.500ms="referral_code"
                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $referrer_name ? 'border-green-400 bg-green-50/50' : 'border-gray-200 focus:border-brand-black100' }}"
                               placeholder="Varsa referans kodunuzu giriniz">
                        @if($referrer_name)
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    @if($referrer_name)
                        <p class="text-green-600 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Referans: {{ $referrer_name }}
                        </p>
                    @elseif($referral_code && !$referrer_name)
                        <p class="text-orange-500 text-xs mt-1">Referans kodu bulunamadı.</p>
                    @endif
                </div>

                {{-- Kullanım Şartları --}}
                <div>
                    <label class="flex items-start">
                        <input type="checkbox" wire:model="terms" class="rounded border-gray-300 text-brand-black100 focus:ring-brand-black100 mt-0.5">
                        <span class="ml-2 text-gray-400 text-xs leading-relaxed">
                            <a href="{{ route('kullanim-kosullari') }}" target="_blank" class="text-brand-black100 hover:underline">Kullanım koşullarını</a> ve
                            <a href="{{ route('gizlilik-kvkk') }}" target="_blank" class="text-brand-black100 hover:underline">KVKK ve Gizlilik Sözleşmesini</a> okudum, kabul ediyorum.
                        </span>
                    </label>
                    @error('terms') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- SMS Hata Mesajı --}}
                @if($sms_error)
                    <div class="bg-red-50 border border-red-200 text-red-600 text-xs px-4 py-3 rounded">
                        {{ $sms_error }}
                    </div>
                @endif

                {{-- Devam Et Butonu --}}
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-wait"
                        class="w-full bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition flex items-center justify-center">
                    <svg wire:loading wire:target="sendVerification" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="sendVerification">Devam Et</span>
                    <span wire:loading wire:target="sendVerification">SMS gönderiliyor...</span>
                </button>
            </form>

            <div class="text-center mt-8">
                <p class="text-gray-400 text-xs">
                    Zaten hesabınız var mı?
                    <a href="{{ route('login') }}" class="text-brand-black100 font-medium hover:underline">Giriş Yap</a>
                </p>
            </div>
        </div>
        @endif

        {{-- STEP 2: SMS Verification --}}
        @if($step === 2)
        <div>
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-brand-black100/5 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-brand-black100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-light text-brand-black100">Telefon <span class="font-semibold">Doğrulama</span></h1>
                <p class="text-gray-400 text-xs mt-2">
                    <span class="font-medium text-brand-black100">+90 {{ $phone }}</span> numarasına gönderilen 6 haneli kodu giriniz
                </p>
            </div>

            <form wire:submit="verifyAndRegister" class="space-y-5">
                {{-- Verification Code Input --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-2 text-center">Doğrulama Kodu</label>
                    <input type="text" wire:model="verification_code"
                           maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                           class="w-full border text-center text-2xl tracking-[0.5em] font-mono px-4 py-3 focus:outline-none transition {{ $errors->has('verification_code') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                           placeholder="000000"
                           autofocus>
                    @error('verification_code') <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p> @enderror
                </div>

                {{-- SMS Hata --}}
                @if($sms_error)
                    <div class="bg-red-50 border border-red-200 text-red-600 text-xs px-4 py-3 rounded text-center">
                        {{ $sms_error }}
                    </div>
                @endif

                {{-- Resend Success --}}
                <div x-show="showResent" x-transition class="bg-green-50 border border-green-200 text-green-600 text-xs px-4 py-3 rounded text-center">
                    Yeni doğrulama kodu gönderildi!
                </div>

                {{-- Countdown & Resend --}}
                <div class="text-center">
                    <template x-if="countdown > 0">
                        <p class="text-gray-400 text-xs">
                            Yeni kod gönder: <span class="font-mono font-medium text-brand-black100" x-text="formatTime(countdown)"></span>
                        </p>
                    </template>
                    <template x-if="countdown <= 0">
                        <button type="button" wire:click="resendCode"
                                wire:loading.attr="disabled"
                                class="text-brand-black100 text-xs font-medium hover:underline">
                            <span wire:loading.remove wire:target="resendCode">Tekrar Kod Gönder</span>
                            <span wire:loading wire:target="resendCode">Gönderiliyor...</span>
                        </button>
                    </template>
                </div>

                {{-- Doğrula Butonu --}}
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-wait"
                        class="w-full bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition flex items-center justify-center">
                    <svg wire:loading wire:target="verifyAndRegister" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="verifyAndRegister">Doğrula ve Kayıt Ol</span>
                    <span wire:loading wire:target="verifyAndRegister">Kayıt yapılıyor...</span>
                </button>
            </form>

            {{-- Geri Dön --}}
            <div class="text-center mt-6">
                <button wire:click="backToForm" class="text-gray-400 text-xs hover:text-brand-black100 transition inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Bilgileri düzenle
                </button>
            </div>

            {{-- Debug Info --}}
            @if(config('app.debug'))
                <div class="mt-6 p-3 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-700">
                    <p class="font-medium mb-1">Debug Modu</p>
                    <p>Test için <strong>000000</strong> kodunu kullanabilirsiniz.</p>
                </div>
            @endif
        </div>
        @endif

    </div>
</div>
