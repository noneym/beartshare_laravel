{{--
    Ana sayfa eser kartı (Öne Çıkan / Son Eklenen): görsel, sanatçı, eser bilgisi, fiyat.
    Değişkenler: $artwork, $key (wire:key öneki), $imageBg (görsel arka planı, ops.)
--}}
<div class="group" wire:key="{{ $key }}-{{ $artwork->id }}">
    <a href="{{ route('artwork.detail', $artwork->slug) }}" class="block">
        <div class="relative {{ $imageBg ?? 'bg-gray-50' }} overflow-hidden aspect-[4/3] mb-4">
            @if($artwork->is_sold)
                <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] px-3 py-1 z-10 uppercase tracking-wider">Satıldı</span>
            @elseif($artwork->is_reserved)
                <span class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] px-3 py-1 z-10 uppercase tracking-wider">Rezerve</span>
            @endif
            @if($artwork->list_image)
                <img src="{{ $artwork->list_image_url }}" srcset="{{ $artwork->list_image_srcset }}" sizes="(min-width: 1024px) 25vw, (min-width: 768px) 33vw, 50vw" alt="{{ $artwork->image_alt }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy" decoding="async">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-300"></div>
        </div>
    </a>
    {{-- phones show two per row: price under the name there, beside it from sm up --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-0">
        <div class="flex-1 min-w-0 sm:pr-4">
            <h3 class="font-medium text-brand-black100 text-sm truncate">{{ $artwork->artist->name }}</h3>
            <p class="text-gray-500 text-xs mt-0.5">{{ $artwork->artist->life_span }}</p>
            <p class="text-gray-400 text-xs mt-1 truncate">{{ $artwork->title }}</p>
            <p class="text-gray-300 text-[10px] mt-0.5">{{ $artwork->technique }}, {{ $artwork->year }}</p>
            <p class="text-gray-300 text-[10px]">{{ $artwork->dimensions }}</p>
            @if($artwork->extra_note)<p class="text-gray-400 text-[10px] leading-snug mt-0.5 line-clamp-2" title="{{ $artwork->extra_note }}">{{ $artwork->extra_note }}</p>@endif
        </div>
        <div class="sm:text-right flex-shrink-0">
            <x-artwork-price :artwork="$artwork" tip-align="start" />
            <x-credit-card-badge :artwork="$artwork" />
        </div>
    </div>
</div>
