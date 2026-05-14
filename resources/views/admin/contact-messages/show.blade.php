<x-admin.layouts.app>
    <x-slot name="title">Mesaj Detayı</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.contact-messages.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Mesaj listesi</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">İletişim Mesajı #{{ $message->id }}</h1>
        </div>
        <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}"
              onsubmit="return confirm('Bu mesajı silmek istediğinize emin misiniz?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">Sil</button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-4">Mesaj İçeriği</h2>

            <table class="w-full text-sm mb-6">
                <tr>
                    <td class="py-2 text-gray-500 w-32">Gönderen:</td>
                    <td class="py-2 font-medium text-gray-800">{{ $message->name }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">E-posta:</td>
                    <td class="py-2"><a href="mailto:{{ $message->email }}" class="text-primary hover:underline">{{ $message->email }}</a></td>
                </tr>
                @if($message->phone)
                <tr>
                    <td class="py-2 text-gray-500">Telefon:</td>
                    <td class="py-2"><a href="tel:{{ $message->phone }}" class="text-gray-800">{{ $message->phone }}</a></td>
                </tr>
                @endif
                <tr>
                    <td class="py-2 text-gray-500">Konu:</td>
                    <td class="py-2 text-gray-800">{{ $message->subject_label }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">Tarih:</td>
                    <td class="py-2 text-gray-800">{{ $message->created_at->format('d.m.Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">IP:</td>
                    <td class="py-2 text-gray-500 font-mono text-xs">{{ $message->ip_address ?? '-' }}</td>
                </tr>
            </table>

            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 mb-2">Mesaj:</p>
                <div class="bg-gray-50 p-4 rounded border-l-4 border-primary text-sm text-gray-700 whitespace-pre-wrap">{{ $message->message }}</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-4">Durum & Notlar</h2>

            <form method="POST" action="{{ route('admin.contact-messages.update', $message) }}">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-xs text-gray-500 mb-1">Durum</label>
                    <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                        @foreach(\App\Models\ContactMessage::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ $message->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs text-gray-500 mb-1">Admin Notları</label>
                    <textarea name="admin_notes" rows="5" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary" placeholder="Dahili notlar...">{{ old('admin_notes', $message->admin_notes) }}</textarea>
                </div>

                @if($message->replied_at)
                    <p class="text-xs text-green-600 mb-3">Yanıtlandı: {{ $message->replied_at->format('d.m.Y H:i') }}</p>
                @endif

                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white py-2 rounded text-sm font-medium">Kaydet</button>
            </form>

            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject_label }}"
                   class="block text-center bg-primary hover:bg-amber-600 text-white py-2 rounded text-sm font-medium">
                    E-posta ile Yanıtla
                </a>
            </div>
        </div>
    </div>
</x-admin.layouts.app>
