<x-admin.layouts.app>
    <x-slot name="title">Bildirim Alıcıları</x-slot>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bildirim Alıcıları</h1>
            <p class="text-sm text-gray-500 mt-1">Yeni sipariş, iletişim mesajı ve eser başvurusu geldiğinde kimlere e-posta / SMS gideceği</p>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-100 text-blue-800 text-sm rounded-lg px-4 py-3 mb-6">
        İletişim formu ve eser başvuruları her zaman <b>{{ config('mail.admin_address') }}</b> adresine de gönderilir.
        Yeni sipariş bildirimi havale siparişinde sipariş oluşunca, kredi kartında ödeme başarılı olunca gider.
        Gönderim sonuçları <a href="{{ route('admin.notification-logs.index') }}" class="underline">Bildirim Log</a>'da.
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
            @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
        </div>
    @endif

    {{-- Alıcılar --}}
    <div class="space-y-4 mb-8">
        @forelse($recipients as $r)
            <div class="bg-white rounded-lg shadow" x-data="{ edit: false }">
                <div class="p-4 flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-800">{{ $r->name }}</span>
                            @unless($r->is_active)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500">Pasif</span>
                            @endunless
                        </div>
                        <div class="text-sm text-gray-600 mt-1 space-x-3">
                            <span class="{{ $r->wantsEmail() ? '' : 'line-through text-gray-400' }}">✉ {{ $r->email ?: 'e-posta yok' }}</span>
                            <span class="{{ $r->wantsSms() ? '' : 'line-through text-gray-400' }}">✆ {{ $r->phone ?: 'telefon yok' }}</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach($r->events ?? [] as $ev)
                                <span class="px-2 py-0.5 text-xs rounded bg-primary/10 text-gray-700">{{ $events[$ev] ?? $ev }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <button type="button" @click="edit = !edit" class="text-primary hover:underline">Düzenle</button>
                        <form method="POST" action="{{ route('admin.notification-recipients.test', $r) }}" onsubmit="return confirm('{{ $r->name }} kişisine seçili kanallardan deneme bildirimi gönderilsin mi?')">
                            @csrf
                            <button class="text-gray-600 hover:underline">Deneme gönder</button>
                        </form>
                        <form method="POST" action="{{ route('admin.notification-recipients.destroy', $r) }}" onsubmit="return confirm('Alıcı silinsin mi?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Sil</button>
                        </form>
                    </div>
                </div>
                <div x-show="edit" x-cloak class="border-t border-gray-100 p-4">
                    @include('admin.notification-recipients.form', ['recipient' => $r, 'action' => route('admin.notification-recipients.update', $r), 'method' => 'PUT', 'submit' => 'Kaydet'])
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-400">Henüz alıcı yok.</div>
        @endforelse
    </div>

    {{-- Yeni alıcı --}}
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="font-semibold text-gray-800 mb-4">Yeni Alıcı Ekle</h2>
        @include('admin.notification-recipients.form', ['recipient' => null, 'action' => route('admin.notification-recipients.store'), 'method' => 'POST', 'submit' => 'Ekle'])
    </div>
</x-admin.layouts.app>
