<x-admin.layouts.app>
    <x-slot name="title">Eser Başvurusu Detayı</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.artwork-submissions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Başvuru listesi</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">Eser Başvurusu #{{ $submission->id }}</h1>
        </div>
        <form method="POST" action="{{ route('admin.artwork-submissions.destroy', $submission) }}"
              onsubmit="return confirm('Bu başvuruyu silmek istediğinize emin misiniz?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">Sil</button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Başvuran Bilgileri --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase mb-4">Başvuran Bilgileri</h2>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-2 text-gray-500 w-32">Ad Soyad:</td>
                        <td class="py-2 font-medium text-gray-800">{{ $submission->name }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500">E-posta:</td>
                        <td class="py-2"><a href="mailto:{{ $submission->email }}" class="text-primary hover:underline">{{ $submission->email }}</a></td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500">Telefon:</td>
                        <td class="py-2"><a href="tel:{{ $submission->phone }}" class="text-gray-800">{{ $submission->phone }}</a></td>
                    </tr>
                </table>
            </div>

            {{-- Eser Bilgileri --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase mb-4">Eser Bilgileri</h2>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-2 text-gray-500 w-32">Sanatçı:</td>
                        <td class="py-2 font-medium text-gray-800">{{ $submission->artist_name }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500">Eser Adı:</td>
                        <td class="py-2 text-gray-800">{{ $submission->artwork_title }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500">Teknik:</td>
                        <td class="py-2 text-gray-800">{{ $submission->technique ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500">Boyut:</td>
                        <td class="py-2 text-gray-800">{{ $submission->dimensions ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500">Yıl:</td>
                        <td class="py-2 text-gray-800">{{ $submission->year ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500">Beklenen Fiyat:</td>
                        <td class="py-2 text-gray-800 font-medium">{{ $submission->expected_price ?? '-' }}</td>
                    </tr>
                </table>

                @if($submission->notes)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500 mb-2">Başvuru Notu:</p>
                        <div class="bg-gray-50 p-3 rounded text-sm text-gray-700 whitespace-pre-wrap">{{ $submission->notes }}</div>
                    </div>
                @endif
            </div>

            {{-- Görseller --}}
            @if(is_array($submission->images) && count($submission->images) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase mb-4">Görseller ({{ count($submission->images) }})</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($submission->images as $img)
                            <a href="{{ asset('storage/' . $img) }}" target="_blank" class="block aspect-square bg-gray-100 overflow-hidden rounded hover:opacity-90 transition">
                                <img src="{{ asset('storage/' . $img) }}" alt="Eser görseli" class="w-full h-full object-cover">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Yan panel: Durum & notlar --}}
        <div class="bg-white rounded-lg shadow p-6 h-fit">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-4">Durum & Notlar</h2>

            <div class="text-xs text-gray-500 mb-4">
                <p>Başvuru tarihi: <strong>{{ $submission->created_at->format('d.m.Y H:i') }}</strong></p>
                @if($submission->ip_address)
                    <p>IP: <span class="font-mono">{{ $submission->ip_address }}</span></p>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.artwork-submissions.update', $submission) }}">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-xs text-gray-500 mb-1">Durum</label>
                    <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                        @foreach(\App\Models\ArtworkSubmission::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ $submission->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs text-gray-500 mb-1">Admin Notları</label>
                    <textarea name="admin_notes" rows="6" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary" placeholder="Dahili notlar...">{{ old('admin_notes', $submission->admin_notes) }}</textarea>
                </div>

                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white py-2 rounded text-sm font-medium">Kaydet</button>
            </form>

            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                <a href="mailto:{{ $submission->email }}?subject=Re: Eser Başvurunuz - {{ $submission->artwork_title }}"
                   class="block text-center bg-primary hover:bg-amber-600 text-white py-2 rounded text-sm font-medium">
                    E-posta ile Yanıtla
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $submission->phone) }}" target="_blank"
                   class="block text-center bg-[#25D366] hover:bg-[#20bd5a] text-white py-2 rounded text-sm font-medium">
                    WhatsApp ile İletişim
                </a>
            </div>
        </div>
    </div>
</x-admin.layouts.app>
