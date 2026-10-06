<div class="bg-white border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider">Passkey ile Giriş</h3>
        @if($passkeys->isNotEmpty())
            <span class="text-[11px] px-2 py-0.5 bg-green-50 text-green-700 border border-green-100">{{ $passkeys->count() }} passkey</span>
        @endif
    </div>

    <p class="text-xs text-gray-500 mb-4 leading-relaxed">
        Passkey ile şifre yazmadan, Face ID, parmak izi ya da cihaz PIN’inizle giriş yaparsınız. Passkey cihazınızda saklanır, iCloud / Google hesabınız üzerinden diğer cihazlarınıza da senkronlanır. Şifreniz geçerli olmaya devam eder.
    </p>

    @if(session('passkey_status'))
        <div class="mb-4 text-xs text-gray-700 bg-gray-50 border border-gray-200 px-3 py-2">{{ session('passkey_status') }}</div>
    @endif

    @if($passkeys->isNotEmpty())
        <ul class="divide-y divide-gray-50 border border-gray-100 mb-4">
            @foreach($passkeys as $pk)
                <li class="flex items-center justify-between gap-3 px-3 py-2.5" wire:key="pk-{{ $pk->id }}">
                    <div class="min-w-0">
                        <p class="text-sm text-brand-black100 truncate">{{ $pk->name }}</p>
                        <p class="text-[11px] text-gray-400">
                            Eklendi {{ $pk->created_at->format('d.m.Y') }}
                            · {{ $pk->last_used_at ? 'Son kullanım ' . $pk->last_used_at->format('d.m.Y H:i') : 'Henüz kullanılmadı' }}
                        </p>
                    </div>
                    @if($confirmDelete === $pk->id)
                        <div class="flex items-center gap-3 text-xs shrink-0">
                            <button wire:click="delete({{ $pk->id }})" class="text-red-600 hover:underline">Sil</button>
                            <button wire:click="$set('confirmDelete', null)" class="text-gray-400 hover:underline">Vazgeç</button>
                        </div>
                    @else
                        <button wire:click="$set('confirmDelete', {{ $pk->id }})" class="text-xs text-gray-400 hover:text-red-600 shrink-0">Kaldır</button>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    @if($passkeys->count() < $max)
        <div x-data="{ ok: false, busy: false, error: '' }" x-init="ok = window.Passkey && Passkey.supported()">
            <template x-if="ok">
                <button type="button" :disabled="busy"
                        @click="busy = true; error = ''; Passkey.register().then(r => { busy = false; $wire.added(r.name) }).catch(e => { error = e.message; busy = false; })"
                        class="border border-gray-200 hover:border-brand-black100 px-4 py-2.5 text-sm text-brand-black100 transition disabled:opacity-60">
                    <span x-text="busy ? 'Bekleniyor…' : '+ Bu cihaza passkey ekle'">+ Bu cihaza passkey ekle</span>
                </button>
            </template>
            <p x-show="!ok" class="text-xs text-gray-400">Bu tarayıcı passkey desteklemiyor. Güncel bir tarayıcı ya da telefon kullanın.</p>
            <p x-show="error" x-text="error" class="text-red-500 text-xs mt-2"></p>
        </div>
    @endif

    @include('partials.passkey-script')
</div>
