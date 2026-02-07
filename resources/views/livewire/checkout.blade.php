<div x-data="{
        showMesafeliSatis: false,
        showOnBilgilendirme: false
     }"
     x-on:close-address-modal.window="$wire.showAddressModal = false"
>
    {{-- Sipariş Tamamlandı --}}
    @if($orderCompleted && $completedOrder)
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
                    <h1 class="text-2xl font-light text-brand-black100 mb-2">Siparişiniz Başarıyla <span class="font-semibold">Alındı!</span></h1>
                    <p class="text-gray-400 text-sm">Sipariş numaranız: <strong class="text-brand-black100">{{ $completedOrder->order_number }}</strong></p>
                </div>

                @if($completedOrder->payment_method === 'havale')
                {{-- Ödeme Bilgileri Kartı --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
                            Havale / EFT Ödeme Bilgileri
                        </h3>
                    </div>
                    <div class="p-6">
                        {{-- Ödeme Kodu --}}
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5 text-center">
                            <p class="text-xs text-amber-600 mb-1">Havale Açıklama Kodunuz</p>
                            <p class="text-2xl font-mono font-bold text-amber-800 tracking-wider">{{ $completedOrder->payment_code }}</p>
                            <p class="text-xs text-amber-500 mt-1">Bu kodu havale/EFT açıklamasına mutlaka yazınız</p>
                        </div>

                        {{-- Ödeme Özeti --}}
                        <div class="bg-gray-50 rounded-lg p-4 mb-5 space-y-2">
                            @php
                                $subtotalTl = $completedOrder->total_tl + ($completedOrder->discount_tl ?? 0);
                            @endphp
                            @if($completedOrder->artpuan_used > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Ara Toplam:</span>
                                <span class="text-gray-700">{{ number_format($subtotalTl, 0, ',', '.') }} TL</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">ArtPuan İndirimi ({{ number_format($completedOrder->artpuan_used, 2, ',', '.') }} AP):</span>
                                <span class="font-medium text-green-600">-{{ number_format($completedOrder->discount_tl, 0, ',', '.') }} TL</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2"></div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-700 font-semibold">Ödenecek Tutar:</span>
                                <span class="text-lg font-bold text-brand-black100">{{ number_format($completedOrder->total_tl, 0, ',', '.') }} TL</span>
                            </div>
                        </div>

                        {{-- Banka Hesapları --}}
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Banka Hesaplarımız</h4>
                        <p class="text-xs text-gray-400 mb-3">Hesap Sahibi: <strong class="text-gray-600">BEARTSHARE ONLİNE SANAT GALERİSİ ANONİM ŞİRKETİ</strong></p>

                        <div class="space-y-3">
                            {{-- Garanti Bankası --}}
                            <div class="border border-green-100 rounded-lg p-4 bg-green-50/50">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 bg-green-600 rounded flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11"/></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-green-800">Garanti Bankası</span>
                                    <span class="text-xs text-green-600">- Galatasaray Şubesi</span>
                                </div>
                                <div class="text-xs text-gray-500 mb-1">Hesap No: <span class="font-mono text-gray-700">068-6291752</span></div>
                                <div class="text-xs text-gray-500">IBAN: <span class="font-mono font-semibold text-gray-700">TR62 0006 2000 0680 0006 2917 52</span></div>
                            </div>

                            {{-- Vakıflar Bankası --}}
                            <div class="border border-blue-100 rounded-lg p-4 bg-blue-50/50">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 bg-blue-700 rounded flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11"/></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-blue-800">Vakıflar Bankası</span>
                                    <span class="text-xs text-blue-600">- Levent Şubesi</span>
                                </div>
                                <div class="text-xs text-gray-500">IBAN: <span class="font-mono font-semibold text-gray-700">TR68 0001 5001 5800 7321 4175 83</span></div>
                            </div>
                        </div>

                        {{-- Tüm Hesaplar Linki --}}
                        <div class="text-center mt-4">
                            <a href="{{ route('banka-hesaplari') }}" class="text-xs text-amber-600 hover:text-amber-800 underline transition">
                                Tüm banka hesap bilgilerimizi görüntüleyin &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Uyarılar --}}
                <div class="space-y-3 mb-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-800 mb-1">30 Dakika Rezervasyon Süresi</p>
                            <p class="text-xs text-red-600">Seçtiğiniz eserler sipariş oluşturulduktan itibaren 30 dakika boyunca sizin için rezerve edilir. Bu süre içinde ödemenizi gerçekleştirmeniz gerekmektedir. Süre dolduğunda eserler yeniden satışa açılacaktır.</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-blue-800 mb-1">Ödeme Dekontu</p>
                            <p class="text-xs text-blue-600">Ödemenizi yaptıktan sonra dekontunuzu <a href="mailto:info@beartshare.com" class="underline font-medium">info@beartshare.com</a> adresine gönderebilirsiniz.</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($completedOrder->artpuan_used > 0)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-2 text-sm text-green-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <span><strong>{{ number_format($completedOrder->artpuan_used, 2, ',', '.') }} ArtPuan</strong> kullanıldı ve <strong>{{ number_format($completedOrder->discount_tl, 0, ',', '.') }} TL</strong> indirim uygulandı.</span>
                    </div>
                </div>
                @endif

                {{-- Butonlar --}}
                <div class="flex gap-3">
                    <a href="{{ route('home') }}" class="flex-1 bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition text-center rounded-lg">
                        Ana Sayfa
                    </a>
                    <a href="{{ route('artworks') }}" class="flex-1 border border-gray-200 py-3 text-sm text-gray-500 hover:bg-gray-50 transition text-center rounded-lg">
                        Eserleri Keşfet
                    </a>
                </div>

            </div>
        </div>
    </div>

    @else
    {{-- Checkout Form --}}
    <section class="bg-brand-black100 py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-light text-white">Sipariş <span class="font-semibold">Tamamla</span></h1>
            <p class="text-white/50 text-sm mt-2">Bilgilerinizi kontrol edin ve siparişinizi tamamlayın</p>
        </div>
    </section>

    <div class="container mx-auto px-4 py-10">
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit="placeOrder">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Sol: Bilgi Formları --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Kişisel Bilgiler --}}
                    <div class="border border-gray-100 p-6">
                        <h2 class="text-sm font-semibold text-brand-black100 mb-6 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Kişisel Bilgiler
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Ad Soyad --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">Ad Soyad</label>
                                <input type="text" value="{{ $customer_name }}" disabled
                                       class="w-full border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed">
                            </div>

                            {{-- E-posta --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">E-posta</label>
                                <input type="email" value="{{ $customer_email }}" disabled
                                       class="w-full border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed">
                            </div>

                            {{-- Telefon --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">Telefon</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 border border-r-0 border-gray-200 bg-gray-100 text-gray-500 text-sm">+90</span>
                                    <input type="tel" value="{{ $customer_phone }}" disabled
                                           class="w-full border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed">
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">
                            <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Bu bilgiler hesabınızdan alınmıştır. Değiştirmek için <a href="{{ route('profile', 'ayarlar') }}" class="text-primary hover:underline">hesap ayarlarını</a> ziyaret edin.
                        </p>
                    </div>

                    {{-- Teslimat Adresi Seçimi --}}
                    <div class="border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Teslimat Adresi
                            </h2>
                            <button type="button" wire:click="openAddressModal('shipping')" class="text-xs text-primary hover:text-primary-dark transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Yeni Adres Ekle
                            </button>
                        </div>

                        @if($shippingAddresses->count() > 0)
                            <div class="space-y-3">
                                @foreach($shippingAddresses as $address)
                                    <label class="flex items-start gap-3 p-4 border cursor-pointer transition
                                        {{ $selectedShippingAddressId == $address->id ? 'border-brand-black100 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}"
                                        wire:key="shipping-addr-{{ $address->id }}">
                                        <input type="radio" wire:model.live="selectedShippingAddressId" value="{{ $address->id }}"
                                               class="mt-0.5 text-brand-black100 focus:ring-brand-black100">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-sm font-medium text-brand-black100">{{ $address->title ?: 'Teslimat Adresi' }}</span>
                                                @if($address->is_default)
                                                    <span class="text-[10px] bg-primary/10 text-primary px-1.5 py-0.5 font-medium">Varsayılan</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500">{{ $address->full_name }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $address->address_line }}, {{ $address->district }}/{{ $address->city }}</p>
                                            @if($address->phone)
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $address->phone }}</p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm text-gray-400 mb-3">Kayıtlı teslimat adresiniz yok.</p>
                                <button type="button" wire:click="openAddressModal('shipping')" class="inline-flex items-center gap-1.5 text-xs bg-brand-black100 text-white px-4 py-2 hover:bg-black transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Adres Ekle
                                </button>
                            </div>
                        @endif
                        @error('selectedShippingAddressId') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Fatura Adresi --}}
                    <div class="border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Fatura Adresi
                            </h2>
                            @if(!$sameBillingAddress)
                                <button type="button" wire:click="openAddressModal('billing')" class="text-xs text-primary hover:text-primary-dark transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Yeni Fatura Adresi
                                </button>
                            @endif
                        </div>

                        {{-- Teslimat adresiyle aynı checkbox --}}
                        <label class="flex items-center gap-2 mb-4 cursor-pointer">
                            <input type="checkbox" wire:model.live="sameBillingAddress"
                                   class="rounded border-gray-300 text-brand-black100 focus:ring-brand-black100">
                            <span class="text-sm text-gray-600">Teslimat adresiyle aynı</span>
                        </label>

                        @if(!$sameBillingAddress)
                            @if($billingAddresses->count() > 0)
                                <div class="space-y-3">
                                    @foreach($billingAddresses as $address)
                                        <label class="flex items-start gap-3 p-4 border cursor-pointer transition
                                            {{ $selectedBillingAddressId == $address->id ? 'border-brand-black100 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}"
                                            wire:key="billing-addr-{{ $address->id }}">
                                            <input type="radio" wire:model.live="selectedBillingAddressId" value="{{ $address->id }}"
                                                   class="mt-0.5 text-brand-black100 focus:ring-brand-black100">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-sm font-medium text-brand-black100">{{ $address->title ?: 'Fatura Adresi' }}</span>
                                                    @if($address->is_default)
                                                        <span class="text-[10px] bg-primary/10 text-primary px-1.5 py-0.5 font-medium">Varsayılan</span>
                                                    @endif
                                                    <span class="text-[10px] px-1.5 py-0.5 font-medium {{ $address->invoice_type === 'corporate' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                                                        {{ $address->invoice_type === 'corporate' ? 'Kurumsal' : 'Bireysel' }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500">{{ $address->full_name }}</p>
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $address->address_line }}, {{ $address->district }}/{{ $address->city }}</p>
                                                @if($address->invoice_type === 'corporate' && $address->company_name)
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ $address->company_name }} | {{ $address->tax_office }} V.D. {{ $address->tax_number }}</p>
                                                @elseif($address->tc_no)
                                                    <p class="text-xs text-gray-400 mt-0.5">TC: {{ $address->tc_no }}</p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50">
                                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm text-gray-400 mb-3">Kayıtlı fatura adresiniz yok.</p>
                                    <button type="button" wire:click="openAddressModal('billing')" class="inline-flex items-center gap-1.5 text-xs bg-brand-black100 text-white px-4 py-2 hover:bg-black transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Fatura Adresi Ekle
                                    </button>
                                </div>
                            @endif
                            @error('selectedBillingAddressId') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    {{-- Sipariş Notu --}}
                    <div class="border border-gray-100 p-6">
                        <h2 class="text-sm font-semibold text-brand-black100 mb-4 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Sipariş Notu
                        </h2>
                        <textarea wire:model="notes" rows="2"
                                  class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition resize-none"
                                  placeholder="Siparişinizle ilgili eklemek istediğiniz notlar... (opsiyonel)"></textarea>
                    </div>

                    {{-- Ödeme Yöntemi --}}
                    <div class="border border-gray-100 p-6">
                        <h2 class="text-sm font-semibold text-brand-black100 mb-6 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Ödeme Yöntemi
                        </h2>

                        <div class="space-y-3">
                            {{-- Havale / EFT --}}
                            <label class="flex items-start gap-3 p-4 border cursor-pointer transition
                                {{ $payment_method === 'havale' ? 'border-brand-black100 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="payment_method" value="havale"
                                       class="mt-0.5 text-brand-black100 focus:ring-brand-black100">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-brand-black100">Havale / EFT</span>
                                    <p class="text-xs text-gray-400 mt-1">Banka havalesi veya EFT ile ödeme yapabilirsiniz. Sipariş sonrası size özel bir açıklama kodu verilecektir.</p>
                                </div>
                            </label>

                            {{-- Kredi Kartı (3D Secure) - Sadece tüm ürünler kredi kartı ile alınabilirse göster --}}
                            @if($allowCreditCard)
                            <label class="flex items-start gap-3 p-4 border cursor-pointer transition
                                {{ $payment_method === 'kredi_karti' ? 'border-brand-black100 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="payment_method" value="kredi_karti"
                                       class="mt-0.5 text-brand-black100 focus:ring-brand-black100">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-brand-black100">Kredi Kartı</span>
                                        <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 uppercase tracking-wider font-medium">3D Secure</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">Garanti Bankası güvenli ödeme altyapısı ile kredi kartınızla güvenle ödeme yapabilirsiniz.</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/80px-Visa_Inc._logo.svg.png" alt="Visa" class="h-4 opacity-60">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/80px-Mastercard-logo.svg.png" alt="Mastercard" class="h-4 opacity-60">
                                    </div>
                                </div>
                            </label>
                            @else
                            <div class="flex items-start gap-3 p-4 border border-amber-200 bg-amber-50 rounded">
                                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-amber-800">Kredi Kartı Kullanılamaz</span>
                                    <p class="text-xs text-amber-600 mt-1">Sepetinizdeki bazı eserler sadece havale/EFT ile satın alınabilir.</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Sağ: Sipariş Özeti --}}
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 p-6 sticky top-24">
                        <h2 class="text-sm font-semibold text-brand-black100 mb-6 uppercase tracking-wider">Sipariş Özeti</h2>

                        {{-- Ürünler --}}
                        <div class="space-y-3 mb-6">
                            @foreach($cartItems as $item)
                                <div class="flex gap-3" wire:key="checkout-item-{{ $item->id }}">
                                    <div class="w-16 h-16 bg-white overflow-hidden flex-shrink-0 border border-gray-100">
                                        @if($item->artwork->first_image)
                                            <img src="{{ $item->artwork->first_image_url }}" alt="{{ $item->artwork->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-brand-black100 truncate">{{ $item->artwork->title }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $item->artwork->artist->name ?? '' }}</p>
                                        <p class="text-xs font-medium text-brand-black100 mt-1">{{ $item->artwork->formatted_price_tl }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-200 pt-4 space-y-3 mb-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Ara Toplam</span>
                                <span class="font-medium text-brand-black100">{{ number_format($totalTl, 0, ',', '.') }} TL</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-400"></span>
                                <span class="text-gray-400">{{ number_format($totalUsd, 0, ',', '.') }} $</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Kargo</span>
                                <span class="text-green-600 text-xs font-medium">Ücretsiz</span>
                            </div>
                        </div>

                        {{-- ArtPuan Kullan --}}
                        @if($userArtPuan > 0)
                        <div class="border border-primary/30 bg-amber-50/50 p-4 mb-4">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" wire:model.live="useArtPuan"
                                       class="rounded border-primary/50 text-primary focus:ring-primary mt-0.5">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-brand-black100">ArtPuan Kullan</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1">
                                        Bakiyeniz: <strong class="text-primary">{{ number_format($userArtPuan, 2, ',', '.') }} AP</strong>
                                    </p>
                                    @if($useArtPuan)
                                        <p class="text-[11px] text-green-600 mt-1 font-medium">
                                            -{{ number_format($artpuanDiscount, 2, ',', '.') }} TL indirim uygulanacak
                                        </p>
                                    @endif
                                </div>
                            </label>
                        </div>
                        @endif

                        {{-- ArtPuan İndirimi --}}
                        @if($useArtPuan && $artpuanDiscount > 0)
                        <div class="border-t border-gray-200 pt-3 mb-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-green-600 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    ArtPuan İndirimi
                                </span>
                                <span class="font-medium text-green-600">-{{ number_format($artpuanDiscount, 2, ',', '.') }} TL</span>
                            </div>
                        </div>
                        @endif

                        <div class="border-t border-gray-200 pt-4 mb-6">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-brand-black100">Toplam</span>
                                <div class="text-right">
                                    @if($useArtPuan && $artpuanDiscount > 0)
                                        <p class="text-xs text-gray-400 line-through">{{ number_format($totalTl, 0, ',', '.') }} TL</p>
                                        <p class="font-semibold text-brand-black100">{{ number_format($finalTotal, 0, ',', '.') }} TL</p>
                                    @else
                                        <p class="font-semibold text-brand-black100">{{ number_format($totalTl, 0, ',', '.') }} TL</p>
                                    @endif
                                    <p class="text-gray-400 text-xs">{{ number_format($totalUsd, 0, ',', '.') }} $</p>
                                </div>
                            </div>
                        </div>

                        {{-- Sözleşmeler --}}
                        <div class="border-t border-gray-200 pt-4 mb-4 space-y-3">
                            <div>
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="mesafeli_satis"
                                           class="rounded border-gray-300 text-brand-black100 focus:ring-brand-black100 mt-0.5">
                                    <span class="text-gray-500 text-xs leading-relaxed">
                                        <button type="button" @click="showMesafeliSatis = true" class="text-brand-black100 font-medium hover:underline">Mesafeli Satış Sözleşmesi</button>'ni okudum ve kabul ediyorum.
                                    </span>
                                </label>
                                @error('mesafeli_satis') <p class="text-red-500 text-xs mt-1 ml-6">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="on_bilgilendirme"
                                           class="rounded border-gray-300 text-brand-black100 focus:ring-brand-black100 mt-0.5">
                                    <span class="text-gray-500 text-xs leading-relaxed">
                                        <button type="button" @click="showOnBilgilendirme = true" class="text-brand-black100 font-medium hover:underline">Ön Bilgilendirme Formu</button>'nu okudum ve kabul ediyorum.
                                    </span>
                                </label>
                                @error('on_bilgilendirme') <p class="text-red-500 text-xs mt-1 ml-6">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Siparişi Tamamla Butonu --}}
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-70 cursor-wait"
                                class="w-full bg-brand-black100 hover:bg-black text-white py-3.5 text-sm font-medium transition flex items-center justify-center">
                            <svg wire:loading wire:target="placeOrder" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="placeOrder">Siparişi Onayla</span>
                            <span wire:loading wire:target="placeOrder">İşleniyor...</span>
                        </button>

                        <a href="{{ route('cart') }}" class="w-full border border-gray-200 mt-3 py-3 text-xs text-gray-500 hover:bg-white transition flex items-center justify-center">
                            Sepete Dön
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Adres Ekleme Modal --}}
    @if($showAddressModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center" x-data>
        <div class="absolute inset-0 bg-black/50" wire:click="closeAddressModal"></div>
        <div class="relative bg-white shadow-2xl w-[95%] max-w-xl max-h-[85vh] flex flex-col"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-brand-black100">
                    {{ $addr_type === 'shipping' ? 'Teslimat Adresi Ekle' : 'Fatura Adresi Ekle' }}
                </h3>
                <button type="button" wire:click="closeAddressModal" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form --}}
            <div class="px-6 py-4 overflow-y-auto space-y-4">
                {{-- Adres Başlığı --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Adres Başlığı *</label>
                    <input type="text" wire:model="addr_title" placeholder="Ev Adresim, İş Adresim..."
                           class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('addr_title') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                    @error('addr_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Ad Soyad --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Ad Soyad *</label>
                        <input type="text" wire:model="addr_full_name"
                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('addr_full_name') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                        @error('addr_full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Telefon --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Telefon</label>
                        <input type="tel" wire:model="addr_phone"
                               class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition">
                    </div>

                    {{-- Şehir --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Şehir *</label>
                        <input type="text" wire:model="addr_city" placeholder="İstanbul"
                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('addr_city') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                        @error('addr_city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- İlçe --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">İlçe *</label>
                        <input type="text" wire:model="addr_district" placeholder="Kadıköy"
                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('addr_district') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                        @error('addr_district') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Adres Satırı --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Adres *</label>
                    <textarea wire:model="addr_address_line" rows="2"
                              placeholder="Mahalle, sokak, bina no, daire no"
                              class="w-full border px-4 py-2.5 text-sm focus:outline-none transition resize-none {{ $errors->has('addr_address_line') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"></textarea>
                    @error('addr_address_line') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Fatura Bilgileri (sadece billing tipinde) --}}
                @if($addr_type === 'billing')
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-semibold text-brand-black100 mb-3 uppercase tracking-wider">Fatura Bilgileri</p>

                        {{-- Fatura Tipi --}}
                        <div class="flex gap-4 mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="addr_invoice_type" value="individual"
                                       class="text-brand-black100 focus:ring-brand-black100">
                                <span class="text-sm text-gray-600">Bireysel</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="addr_invoice_type" value="corporate"
                                       class="text-brand-black100 focus:ring-brand-black100">
                                <span class="text-sm text-gray-600">Kurumsal</span>
                            </label>
                        </div>

                        @if($addr_invoice_type === 'individual')
                            {{-- TC Kimlik No --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">TC Kimlik No *</label>
                                <input type="text" wire:model="addr_tc_no" maxlength="11" inputmode="numeric"
                                       class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('addr_tc_no') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                                @error('addr_tc_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div class="space-y-4">
                                {{-- Şirket Unvanı --}}
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1.5">Şirket Unvanı *</label>
                                    <input type="text" wire:model="addr_company_name"
                                           class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('addr_company_name') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                                    @error('addr_company_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    {{-- Vergi Dairesi --}}
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1.5">Vergi Dairesi *</label>
                                        <input type="text" wire:model="addr_tax_office"
                                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('addr_tax_office') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                                        @error('addr_tax_office') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Vergi Numarası --}}
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1.5">Vergi No *</label>
                                        <input type="text" wire:model="addr_tax_number" maxlength="11" inputmode="numeric"
                                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('addr_tax_number') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                                        @error('addr_tax_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Varsayılan --}}
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="addr_is_default"
                           class="rounded border-gray-300 text-brand-black100 focus:ring-brand-black100">
                    <span class="text-xs text-gray-600">Varsayılan adres olarak kaydet</span>
                </label>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3">
                <button type="button" wire:click="closeAddressModal" class="flex-1 border border-gray-200 py-2.5 text-sm text-gray-500 hover:bg-gray-50 transition">
                    İptal
                </button>
                <button type="button" wire:click="saveNewAddress"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-wait"
                        class="flex-1 bg-brand-black100 hover:bg-black text-white py-2.5 text-sm font-medium transition flex items-center justify-center">
                    <svg wire:loading wire:target="saveNewAddress" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="saveNewAddress">Adresi Kaydet</span>
                    <span wire:loading wire:target="saveNewAddress">Kaydediliyor...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Mesafeli Satış Sözleşmesi Modal --}}
    <div x-show="showMesafeliSatis" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="showMesafeliSatis = false"></div>
        <div x-show="showMesafeliSatis"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white shadow-2xl w-[95%] max-w-2xl max-h-[80vh] flex flex-col"
        >
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-brand-black100">Mesafeli Satış Sözleşmesi</h3>
                <button @click="showMesafeliSatis = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-4 overflow-y-auto text-xs text-gray-600 leading-relaxed space-y-4">
                <p class="font-semibold text-brand-black100">MESAFELİ SATIŞ SÖZLEŞMESİ</p>

                <p class="font-semibold">MADDE 1 - TARAFLAR</p>
                <p><strong>SATICI:</strong></p>
                <p>Unvan: BeArtShare Sanat Galerisi<br>
                Adres: Harmancı Giz Plaza, Harman Sok. No:5 K:21 D:118 Esentepe/İST<br>
                Telefon: 0510 221 64 13<br>
                E-posta: info@beartshare.com</p>

                <p><strong>ALICI:</strong></p>
                <p>Ad Soyad: {{ $customer_name }}<br>
                E-posta: {{ $customer_email }}<br>
                Telefon: {{ $customer_phone }}</p>

                <p class="font-semibold">MADDE 2 - KONU</p>
                <p>İşbu sözleşmenin konusu, ALICI'nın SATICI'ya ait beartshare.com internet sitesinden elektronik ortamda siparişini yaptığı aşağıda nitelikleri ve satış fiyatı belirtilen ürünün satışı ve teslimi ile ilgili olarak 6502 sayılı Tüketicinin Korunması Hakkındaki Kanun ve Mesafeli Sözleşmelere Dair Yönetmelik hükümleri gereğince tarafların hak ve yükümlülüklerinin saptanmasıdır.</p>

                <p class="font-semibold">MADDE 3 - SÖZLEŞME KONUSU ÜRÜN BİLGİLERİ</p>
                <table class="w-full border border-gray-200 text-xs">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-200 px-3 py-2 text-left">Eser</th>
                            <th class="border border-gray-200 px-3 py-2 text-left">Sanatçı</th>
                            <th class="border border-gray-200 px-3 py-2 text-right">Fiyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                        <tr>
                            <td class="border border-gray-200 px-3 py-2">{{ $item->artwork->title }}</td>
                            <td class="border border-gray-200 px-3 py-2">{{ $item->artwork->artist->name ?? '' }}</td>
                            <td class="border border-gray-200 px-3 py-2 text-right">{{ $item->artwork->formatted_price_tl }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="2" class="border border-gray-200 px-3 py-2 text-right">Toplam:</td>
                            <td class="border border-gray-200 px-3 py-2 text-right">{{ number_format($totalTl, 0, ',', '.') }} TL</td>
                        </tr>
                    </tfoot>
                </table>
                <p>Ödeme Şekli: Havale / EFT</p>

                <p class="font-semibold">MADDE 4 - GENEL HÜKÜMLER</p>
                <p>4.1. ALICI, SATICI'ya ait internet sitesinde sözleşme konusu ürünün temel nitelikleri, satış fiyatı ve ödeme şekli ile teslimata ilişkin ön bilgileri okuyup bilgi sahibi olduğunu ve elektronik ortamda gerekli onayı verdiğini kabul eder.</p>
                <p>4.2. Sözleşme konusu ürün, yasal 30 günlük süreyi aşmamak koşulu ile her bir ürün için ALICI'nın yerleşim yerinin uzaklığına bağlı olarak internet sitesinde ön bilgiler içinde açıklanan süre içinde ALICI veya gösterdiği adresteki kişi/kuruluşa teslim edilir.</p>
                <p>4.3. Sözleşme konusu ürün, ALICI'dan başka bir kişi/kuruluşa teslim edilecek ise, teslim edilecek kişi/kuruluşun teslimatı kabul etmemesinden SATICI sorumlu tutulamaz.</p>
                <p>4.4. SATICI, sözleşme konusu ürünün sağlam, eksiksiz, siparişte belirtilen niteliklere uygun teslim edilmesinden sorumludur.</p>

                <p class="font-semibold">MADDE 5 - CAYMA HAKKI</p>
                <p>ALICI, sözleşme konusu ürünün kendisine veya gösterdiği adresteki kişi/kuruluşa teslim tarihinden itibaren 14 (on dört) gün içerisinde cayma hakkına sahiptir. Cayma hakkının kullanılması için bu süre içinde SATICI'ya e-posta ile bildirimde bulunulması ve ürünün MADDE 6 kapsamında belirtilen şartlar dahilinde iade edilmesi gerekmektedir.</p>
                <p>Ancak, 6502 sayılı Kanun'un 15. maddesi uyarınca, fiyatı borsa ya da teşkilatlanmış diğer piyasalarda belirlenen mallara ilişkin sözleşmelerde, tüketicinin istekleri veya açıkça onun kişisel ihtiyaçları doğrultusunda hazırlanan mallara ilişkin sözleşmelerde cayma hakkı kullanılamaz. Sanat eserleri, özellikleri itibarıyla iade edilemeyecek nitelikte olabilir.</p>

                <p class="font-semibold">MADDE 6 - İADE KOŞULLARI</p>
                <p>ALICI'nın cayma hakkını kullanması halinde, iade edilecek ürünün ambalajının açılmamış, bozulmamış ve ürünün kullanılmamış olması gerekmektedir. Sanat eserleri için özel paketleme ve sigortalı kargo ile iade gönderimi zorunludur.</p>

                <p class="font-semibold">MADDE 7 - GENEL HÜKÜMLER</p>
                <p>İşbu sözleşme, ALICI tarafından elektronik ortamda onaylanması ile yürürlüğe girer. Her iki taraf da işbu sözleşmeden doğan uyuşmazlıklarda Tüketici Hakem Heyetleri ve Tüketici Mahkemeleri'ne başvurabilir.</p>

                <p class="text-gray-400 text-[10px] mt-4">Son güncelleme: {{ date('d.m.Y') }}</p>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                <button @click="showMesafeliSatis = false" class="w-full bg-brand-black100 hover:bg-black text-white py-2.5 text-sm font-medium transition">
                    Kapat
                </button>
            </div>
        </div>
    </div>

    {{-- Ön Bilgilendirme Formu Modal --}}
    <div x-show="showOnBilgilendirme" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="showOnBilgilendirme = false"></div>
        <div x-show="showOnBilgilendirme"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white shadow-2xl w-[95%] max-w-2xl max-h-[80vh] flex flex-col"
        >
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-brand-black100">Ön Bilgilendirme Formu</h3>
                <button @click="showOnBilgilendirme = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-4 overflow-y-auto text-xs text-gray-600 leading-relaxed space-y-4">
                <p class="font-semibold text-brand-black100">ÖN BİLGİLENDİRME FORMU</p>
                <p>6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği kapsamında düzenlenen işbu ön bilgilendirme formu aşağıdaki bilgileri içermektedir:</p>

                <p class="font-semibold">1. SATICI BİLGİLERİ</p>
                <p>Unvan: BeArtShare Sanat Galerisi<br>
                Adres: Harmancı Giz Plaza, Harman Sok. No:5 K:21 D:118 Esentepe/İST<br>
                Telefon: 0510 221 64 13<br>
                E-posta: info@beartshare.com</p>

                <p class="font-semibold">2. ÜRÜN BİLGİLERİ</p>
                <p>Sözleşmeye konu ürün/ürünlerin temel özellikleri (türü, miktarı, fiyatı) sipariş sayfasında yer almaktadır.</p>
                <table class="w-full border border-gray-200 text-xs">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-200 px-3 py-2 text-left">Eser</th>
                            <th class="border border-gray-200 px-3 py-2 text-left">Sanatçı</th>
                            <th class="border border-gray-200 px-3 py-2 text-right">Fiyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                        <tr>
                            <td class="border border-gray-200 px-3 py-2">{{ $item->artwork->title }}</td>
                            <td class="border border-gray-200 px-3 py-2">{{ $item->artwork->artist->name ?? '' }}</td>
                            <td class="border border-gray-200 px-3 py-2 text-right">{{ $item->artwork->formatted_price_tl }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="2" class="border border-gray-200 px-3 py-2 text-right">Toplam:</td>
                            <td class="border border-gray-200 px-3 py-2 text-right">{{ number_format($totalTl, 0, ',', '.') }} TL</td>
                        </tr>
                    </tfoot>
                </table>

                <p class="font-semibold">3. ÖDEME VE TESLİMAT</p>
                <p>Ödeme Şekli: Havale / EFT<br>
                Tahmini Teslimat Süresi: Ödemenin onaylanmasından itibaren 7-14 iş günü<br>
                Kargo Ücreti: Ücretsiz</p>

                <p class="font-semibold">4. CAYMA HAKKI</p>
                <p>Tüketici, ürünün kendisine veya gösterdiği adresteki kişi/kuruluşa teslim tarihinden itibaren 14 (on dört) gün içerisinde, herhangi bir gerekçe göstermeksizin ve cezai şart ödemeksizin sözleşmeden cayma hakkına sahiptir.</p>
                <p>Cayma hakkı süresi sona ermeden önce, tüketicinin onayı ile hizmetin ifasına başlanan hizmet sözleşmelerinde cayma hakkı kullanılamaz.</p>
                <p>Sanat eserleri, nitelikleri itibarıyla iade edilemeyecek durumda olabilir. Bu durumlara ilişkin detaylı bilgi Mesafeli Satış Sözleşmesi'nde yer almaktadır.</p>

                <p class="font-semibold">5. CAYMA HAKKININ KULLANILMASI</p>
                <p>Cayma hakkınızı kullanmak istemeniz durumunda, 14 gün içerisinde info@beartshare.com adresine e-posta göndererek veya SATICI'ya yazılı bildirimde bulunarak cayma hakkınızı kullanabilirsiniz. Cayma hakkı bildiriminin bu süre içinde yapılması yeterlidir.</p>

                <p class="font-semibold">6. UYUŞMAZLIK ÇÖZÜMÜ</p>
                <p>İşbu sözleşmeden doğan uyuşmazlıklarda, Gümrük ve Ticaret Bakanlığı'nca ilan edilen değere kadar Tüketici Hakem Heyetleri, bu değerin üzerindeki uyuşmazlıklarda Tüketici Mahkemeleri yetkilidir.</p>

                <p class="font-semibold">7. BİLGİLENDİRME</p>
                <p>ALICI, işbu ön bilgilendirme formunu elektronik ortamda okuyup bilgi sahibi olduğunu ve ardından satın alma işlemini gerçekleştirdiğini kabul ve beyan eder.</p>

                <p class="text-gray-400 text-[10px] mt-4">Son güncelleme: {{ date('d.m.Y') }}</p>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                <button @click="showOnBilgilendirme = false" class="w-full bg-brand-black100 hover:bg-black text-white py-2.5 text-sm font-medium transition">
                    Kapat
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
