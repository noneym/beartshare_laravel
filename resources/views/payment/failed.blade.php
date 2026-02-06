<x-layouts.app
    title="Ödeme Başarısız | BeArtShare"
    metaRobots="noindex, nofollow"
>
    <div class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">

                {{-- Hata Başlığı --}}
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-light text-brand-black100 mb-2">Ödeme <span class="font-semibold">Başarısız</span></h1>
                    <p class="text-gray-400 text-sm">Sipariş numarası: <strong class="text-brand-black100">{{ $order->order_number }}</strong></p>
                </div>

                {{-- Hata Mesajı --}}
                <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-red-800 mb-1">Ödeme İşlemi Tamamlanamadı</h3>
                            <p class="text-sm text-red-600">{{ $error }}</p>
                        </div>
                    </div>
                </div>

                {{-- Ne Yapılabilir --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
                    <h3 class="text-sm font-semibold text-brand-black100 mb-4">Ne Yapabilirsiniz?</h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-semibold text-gray-500">1</span>
                            <span>Kart bilgilerinizi kontrol ederek tekrar deneyin</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-semibold text-gray-500">2</span>
                            <span>Farklı bir kredi kartı ile ödeme yapmayı deneyin</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-semibold text-gray-500">3</span>
                            <span>Kartınızın internet alışverişine açık olduğundan emin olun</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-semibold text-gray-500">4</span>
                            <span>Havale/EFT ile ödeme yapmayı tercih edebilirsiniz</span>
                        </li>
                    </ul>
                </div>

                {{-- Sipariş Bilgisi --}}
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="text-sm font-semibold text-brand-black100 mb-3">Sipariş Bilgileri</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Sipariş No:</span>
                            <span class="font-medium text-brand-black100">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tutar:</span>
                            <span class="font-medium text-brand-black100">{{ number_format($order->total_tl, 0, ',', '.') }} TL</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Durum:</span>
                            <span class="text-amber-600 font-medium">Ödeme Bekleniyor</span>
                        </div>
                    </div>
                </div>

                {{-- İletişim --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-blue-800 mb-1">Yardıma mı İhtiyacınız Var?</p>
                        <p class="text-xs text-blue-600">Sorunlarınız için bize <a href="mailto:info@beartshare.com" class="underline font-medium">info@beartshare.com</a> adresinden veya <a href="tel:05102216413" class="underline font-medium">0510 221 64 13</a> numarasından ulaşabilirsiniz.</p>
                    </div>
                </div>

                {{-- Butonlar --}}
                <div class="flex gap-3">
                    <a href="{{ route('checkout') }}" class="flex-1 bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition text-center rounded-lg">
                        Tekrar Dene
                    </a>
                    <a href="{{ route('profile', 'orders') }}" class="flex-1 border border-gray-200 py-3 text-sm text-gray-500 hover:bg-gray-50 transition text-center rounded-lg">
                        Siparişlerim
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
