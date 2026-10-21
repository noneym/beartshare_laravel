<x-admin.layouts.app>
    <x-slot name="title">Bildirim Şablonları</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bildirim Şablonları</h1>
        <p class="text-sm text-gray-500 mt-1">Müşterilere giden SMS ve e-posta metinleri. Düzenlediğiniz metin kaydedildiği andan itibaren kullanılır.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow divide-y divide-gray-100">
        @foreach($templates as $t)
            <a href="{{ route('admin.notification-templates.edit', $t['key']) }}" class="block p-4 hover:bg-gray-50 transition">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-800">{{ $t['name'] }}</span>
                            @if($t['customized'])
                                <span class="px-2 py-0.5 text-[11px] rounded-full bg-blue-50 text-blue-700">Düzenlendi</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $t['description'] }}</p>
                        @if($t['has_sms'] && $t['sms'])
                            <p class="text-sm text-gray-600 mt-2 line-clamp-2"><span class="text-gray-400">SMS:</span> {{ $t['sms'] }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        @if($t['has_sms'])
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $t['sms_enabled'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500 line-through' }}">SMS</span>
                        @endif
                        @if($t['has_email'])
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $t['email_enabled'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500 line-through' }}">E-posta</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</x-admin.layouts.app>
