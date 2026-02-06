<x-layouts.app
    title="Ödeme Başarılı | BeArtShare"
    metaRobots="noindex, nofollow"
>
    <div class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">

                {{-- Başarı Başlığı --}}
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-light text-brand-black100 mb-2">Ödemeniz <span class="font-semibold">Başarıyla Alındı!</span></h1>
                    <p class="text-gray-400 text-sm">Sipariş numaranız: <strong class="text-brand-black100">{{ $order->order_number }}</strong></p>
                </div>

                {{-- Sipariş Özeti --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Ödeme Onaylandı
                        </h3>
                    </div>
                    <div class="p-6">
                        {{-- Sipariş Ürünleri --}}
                        <div class="space-y-3 mb-6">
                            @foreach($order->items as $item)
                                <div class="flex gap-3">
                                    <div class="w-16 h-16 bg-gray-100 overflow-hidden flex-shrink-0 border border-gray-100 rounded">
                                        @if($item->artwork && $item->artwork->first_image)
                                            <img src="{{ $item->artwork->first_image_url }}" alt="{{ $item->artwork_title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-brand-black100 truncate">{{ $item->artwork_title }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->artist_name }}</p>
                                        <p class="text-sm font-medium text-brand-black100 mt-1">{{ number_format($item->price_tl, 0, ',', '.') }} TL</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Ödeme Özeti --}}
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            @if($order->artpuan_used > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">ArtPuan İndirimi:</span>
                                <span class="font-medium text-green-600">-{{ number_format($order->discount_tl, 0, ',', '.') }} TL</span>
                            </div>
                            @endif
                            <div class="flex justify-between border-t border-gray-200 pt-2">
                                <span class="text-gray-700 font-semibold">Ödenen Tutar:</span>
                                <span class="text-lg font-bold text-green-600">{{ number_format($order->total_tl, 0, ',', '.') }} TL</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Ödeme Yöntemi:</span>
                                <span class="text-gray-500">Kredi Kartı (3D Secure)</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bilgilendirme --}}
                <div class="space-y-3 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-blue-800 mb-1">E-posta Bildirimi</p>
                            <p class="text-xs text-blue-600">Sipariş onayı ve detayları <strong>{{ $order->customer_email }}</strong> adresine gönderildi.</p>
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-green-800 mb-1">Kargo Bilgilendirmesi</p>
                            <p class="text-xs text-green-600">Eseriniz özenle paketlenerek en kısa sürede kargoya verilecektir. Kargo takip numarası e-posta ile paylaşılacaktır.</p>
                        </div>
                    </div>
                </div>

                {{-- ArtPuan Bilgilendirme --}}
                @if($order->artpuan_used > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-2 text-sm text-amber-700">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <span><strong>{{ number_format($order->artpuan_used, 2, ',', '.') }} ArtPuan</strong> kullanıldı.</span>
                    </div>
                </div>
                @endif

                {{-- Kazanılan ArtPuan --}}
                @if(isset($earnedPoints) && $earnedPoints > 0)
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-2 text-sm text-purple-700">
                        <svg class="w-5 h-5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Tebrikler! Bu alışverişten <strong>{{ $earnedPoints }} ArtPuan</strong> kazandınız!</span>
                    </div>
                </div>
                @endif

                {{-- Butonlar --}}
                <div class="flex gap-3">
                    <a href="{{ route('profile', 'orders') }}" class="flex-1 bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition text-center rounded-lg">
                        Siparişlerim
                    </a>
                    <a href="{{ route('artworks') }}" class="flex-1 border border-gray-200 py-3 text-sm text-gray-500 hover:bg-gray-50 transition text-center rounded-lg">
                        Eserleri Keşfet
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
