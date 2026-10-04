{{-- Alıcı ekleme / düzenleme formu --}}
@php $events = \App\Models\NotificationRecipient::EVENTS; @endphp
<form method="POST" action="{{ $action }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <div>
        <label class="block text-xs text-gray-500 mb-1">Ad Soyad *</label>
        <input type="text" name="name" value="{{ $recipient->name ?? '' }}" required maxlength="100"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">E-posta</label>
        <input type="email" name="email" value="{{ $recipient->email ?? '' }}" maxlength="200"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Telefon (5XX XXX XX XX)</label>
        <input type="tel" name="phone" value="{{ $recipient->phone ?? '' }}" maxlength="20" placeholder="5321234567"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
    </div>

    <div>
        <p class="text-xs text-gray-500 mb-2">Kanal</p>
        <label class="flex items-center gap-2 text-sm text-gray-700 mb-1">
            <input type="checkbox" name="via_email" value="1" {{ ($recipient->via_email ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
            E-posta
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="via_sms" value="1" {{ ($recipient->via_sms ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
            SMS
        </label>
    </div>
    <div>
        <p class="text-xs text-gray-500 mb-2">Bildirimler</p>
        @foreach($events as $key => $label)
            <label class="flex items-center gap-2 text-sm text-gray-700 mb-1">
                <input type="checkbox" name="events[]" value="{{ $key }}" {{ in_array($key, $recipient->events ?? ['new_order'], true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                {{ $label }}
            </label>
        @endforeach
    </div>
    <div class="flex flex-col justify-between gap-3">
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="is_active" value="1" {{ ($recipient->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
            Aktif
        </label>
        <div>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded text-sm hover:opacity-90">{{ $submit }}</button>
        </div>
    </div>
</form>
