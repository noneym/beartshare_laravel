<div>
    <!-- Page Header -->
    <section class="bg-brand-black100 py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-light text-white">Adres<span class="font-semibold">lerim</span></h1>
            <p class="text-white/50 text-sm mt-2">Teslimat ve fatura adreslerinizi yönetin</p>
        </div>
    </section>

    <div class="container mx-auto px-4 py-10">
        {{-- Bildirimler --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Adres Ekleme/Düzenleme Formu --}}
        @if($showForm)
        <div class="border border-gray-100 p-6 mb-8">
            <h2 class="text-sm font-semibold text-brand-black100 mb-6 uppercase tracking-wider">
                {{ $editingId ? 'Adresi Düzenle' : 'Yeni Adres Ekle' }}
            </h2>

            <form wire:submit="saveAddress">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Adres Tipi --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Adres Tipi *</label>
                        <select wire:model.live="type"
                                class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 bg-white transition">
                            <option value="shipping">Teslimat Adresi</option>
                            <option value="billing">Fatura Adresi</option>
                        </select>
                    </div>

                    {{-- Adres Başlığı --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Adres Başlığı *</label>
                        <input type="text" wire:model="title"
                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('title') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                               placeholder="Ev Adresim, İş Adresim vb.">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Ad Soyad --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Ad Soyad *</label>
                        <input type="text" wire:model="full_name"
                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('full_name') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                        @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Telefon --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Telefon</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm">+90</span>
                            <input type="tel" wire:model="phone"
                                   class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition">
                        </div>
                    </div>

                    {{-- Şehir --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Şehir *</label>
                        <input type="text" wire:model="city"
                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('city') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                               placeholder="İstanbul">
                        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- İlçe --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">İlçe *</label>
                        <input type="text" wire:model="district"
                               class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('district') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                               placeholder="Kadıköy">
                        @error('district') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Adres --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1.5">Adres *</label>
                        <textarea wire:model="address_line" rows="2"
                                  class="w-full border px-4 py-2.5 text-sm focus:outline-none transition resize-none {{ $errors->has('address_line') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                                  placeholder="Mahalle, sokak, bina no, daire no"></textarea>
                        @error('address_line') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Fatura Bilgileri (billing tipinde göster) --}}
                    @if($type === 'billing')
                    <div class="md:col-span-2 border-t border-gray-100 pt-4 mt-2">
                        <h3 class="text-xs font-semibold text-brand-black100 mb-4 uppercase tracking-wider">Fatura Bilgileri</h3>

                        {{-- Fatura Tipi --}}
                        <div class="flex gap-4 mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="invoice_type" value="individual"
                                       class="text-brand-black100 focus:ring-brand-black100">
                                <span class="text-sm text-gray-600">Bireysel</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="invoice_type" value="corporate"
                                       class="text-brand-black100 focus:ring-brand-black100">
                                <span class="text-sm text-gray-600">Kurumsal</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($invoice_type === 'individual')
                            {{-- TC Kimlik No --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs text-gray-500 mb-1.5">TC Kimlik No *</label>
                                <input type="text" wire:model="tc_no" maxlength="11" inputmode="numeric"
                                       class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('tc_no') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}">
                                @error('tc_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            @else
                            {{-- Şirket Unvanı --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs text-gray-500 mb-1.5">Şirket Unvanı *</label>
                                <input type="text" wire:model="company_name"
                                       class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('company_name') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                                       placeholder="ABC Ticaret Ltd. Şti.">
                                @error('company_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Vergi Dairesi --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">Vergi Dairesi *</label>
                                <input type="text" wire:model="tax_office"
                                       class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('tax_office') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                                       placeholder="Kadıköy V.D.">
                                @error('tax_office') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Vergi Numarası --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">Vergi Numarası *</label>
                                <input type="text" wire:model="tax_number" maxlength="11" inputmode="numeric"
                                       class="w-full border px-4 py-2.5 text-sm focus:outline-none transition {{ $errors->has('tax_number') ? 'border-red-400' : 'border-gray-200 focus:border-brand-black100' }}"
                                       placeholder="1234567890">
                                @error('tax_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Varsayılan --}}
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_default"
                                   class="rounded border-gray-300 text-brand-black100 focus:ring-brand-black100">
                            <span class="text-sm text-gray-600">Varsayılan adres olarak ayarla</span>
                        </label>
                    </div>
                </div>

                {{-- Butonlar --}}
                <div class="flex gap-3 mt-6">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 bg-brand-black100 hover:bg-black text-white text-sm font-medium transition flex items-center gap-2">
                        <svg wire:loading wire:target="saveAddress" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="saveAddress">{{ $editingId ? 'Güncelle' : 'Kaydet' }}</span>
                        <span wire:loading wire:target="saveAddress">Kaydediliyor...</span>
                    </button>
                    <button type="button" wire:click="cancelForm"
                            class="px-6 py-2.5 border border-gray-200 text-gray-500 text-sm hover:bg-gray-50 transition">
                        İptal
                    </button>
                </div>
            </form>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Teslimat Adresleri --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Teslimat Adresleri
                    </h2>
                    @if(!$showForm)
                    <button wire:click="openNewForm('shipping')" class="text-xs text-brand-black100 hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Yeni Ekle
                    </button>
                    @endif
                </div>

                @if($shippingAddresses->isEmpty())
                    <div class="border border-dashed border-gray-200 p-8 text-center">
                        <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-gray-400 text-sm mb-3">Henüz teslimat adresi eklenmemiş</p>
                        @if(!$showForm)
                        <button wire:click="openNewForm('shipping')" class="text-xs text-brand-black100 hover:underline">+ Teslimat Adresi Ekle</button>
                        @endif
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($shippingAddresses as $addr)
                        <div class="border p-4 transition {{ $addr->is_default ? 'border-brand-black100 bg-gray-50/50' : 'border-gray-100' }}" wire:key="ship-{{ $addr->id }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-sm font-medium text-brand-black100">{{ $addr->title }}</h3>
                                        @if($addr->is_default)
                                            <span class="text-[10px] bg-brand-black100 text-white px-2 py-0.5">Varsayılan</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $addr->full_name }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $addr->address_line }}</p>
                                    <p class="text-xs text-gray-400">{{ $addr->district }} / {{ $addr->city }}</p>
                                    @if($addr->phone)
                                        <p class="text-xs text-gray-400 mt-1">{{ $addr->phone }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1 flex-shrink-0 ml-3">
                                    @if(!$addr->is_default)
                                    <button wire:click="setDefault({{ $addr->id }})" title="Varsayılan Yap"
                                            class="p-1.5 text-gray-300 hover:text-brand-black100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    @endif
                                    <button wire:click="editAddress({{ $addr->id }})" title="Düzenle"
                                            class="p-1.5 text-gray-300 hover:text-brand-black100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteAddress({{ $addr->id }})" wire:confirm="Bu adresi silmek istediğinizden emin misiniz?" title="Sil"
                                            class="p-1.5 text-gray-300 hover:text-red-500 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Fatura Adresleri --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-brand-black100 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Fatura Adresleri
                    </h2>
                    @if(!$showForm)
                    <button wire:click="openNewForm('billing')" class="text-xs text-brand-black100 hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Yeni Ekle
                    </button>
                    @endif
                </div>

                @if($billingAddresses->isEmpty())
                    <div class="border border-dashed border-gray-200 p-8 text-center">
                        <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-gray-400 text-sm mb-3">Henüz fatura adresi eklenmemiş</p>
                        @if(!$showForm)
                        <button wire:click="openNewForm('billing')" class="text-xs text-brand-black100 hover:underline">+ Fatura Adresi Ekle</button>
                        @endif
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($billingAddresses as $addr)
                        <div class="border p-4 transition {{ $addr->is_default ? 'border-brand-black100 bg-gray-50/50' : 'border-gray-100' }}" wire:key="bill-{{ $addr->id }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-sm font-medium text-brand-black100">{{ $addr->title }}</h3>
                                        @if($addr->is_default)
                                            <span class="text-[10px] bg-brand-black100 text-white px-2 py-0.5">Varsayılan</span>
                                        @endif
                                        <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5">{{ $addr->invoice_type_label }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $addr->full_name }}</p>
                                    @if($addr->invoice_type === 'corporate')
                                        <p class="text-xs text-gray-500 mt-1">{{ $addr->company_name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $addr->tax_office }} - {{ $addr->tax_number }}</p>
                                    @else
                                        <p class="text-[10px] text-gray-400 mt-0.5">TC: {{ $addr->tc_no }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">{{ $addr->address_line }}</p>
                                    <p class="text-xs text-gray-400">{{ $addr->district }} / {{ $addr->city }}</p>
                                </div>

                                <div class="flex items-center gap-1 flex-shrink-0 ml-3">
                                    @if(!$addr->is_default)
                                    <button wire:click="setDefault({{ $addr->id }})" title="Varsayılan Yap"
                                            class="p-1.5 text-gray-300 hover:text-brand-black100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    @endif
                                    <button wire:click="editAddress({{ $addr->id }})" title="Düzenle"
                                            class="p-1.5 text-gray-300 hover:text-brand-black100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteAddress({{ $addr->id }})" wire:confirm="Bu adresi silmek istediğinizden emin misiniz?" title="Sil"
                                            class="p-1.5 text-gray-300 hover:text-red-500 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
