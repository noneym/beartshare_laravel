@props(['artwork', 'size' => 'card'])

{{--
    Eser fiyatı. Satılmış eserlerde satış fiyatı ve tarihi yalnızca üyelere gösterilir.
    size: detail (eser sayfası) | card (liste kartı, TL + USD) | small (yalnız TL)
--}}
@php
    $sold = $artwork->is_sold;
    $visible = !$sold || auth()->check();
    $soldDate = $sold && $artwork->sold_at ? $artwork->sold_at->format('d.m.Y') : null;
@endphp

@if($visible)
    @if($size === 'detail')
        <div class="flex items-baseline gap-3 mb-1">
            <span class="text-2xl font-semibold text-brand-black100">{{ $artwork->formatted_price_tl }}</span>
            <span class="text-gray-400 text-sm">{{ $artwork->formatted_price_usd }}</span>
            {{-- Masaüstünde üzerine gelince, mobilde dokununca açıklama --}}
            <span class="relative self-center group" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" @click="open = !open" aria-describedby="price-all-inclusive"
                        class="inline-flex items-center gap-1 border border-gray-300 text-gray-600 text-[11px] font-medium px-2 py-0.5 rounded-full hover:border-brand-black100 hover:text-brand-black100 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-black100/30 transition">
                    Her şey dahil
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </button>
                <span id="price-all-inclusive" role="tooltip"
                      :class="open ? 'opacity-100 visible' : ''"
                      class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-max max-w-[220px] bg-brand-black100 text-white text-xs leading-snug px-3 py-2 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition z-20 pointer-events-none">
                    Vergi ve komisyon oranları dahil fiyattır.
                    <span class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-x-4 border-x-transparent border-t-4 border-t-brand-black100"></span>
                </span>
            </span>
        </div>
        @if($sold)
            <p class="text-xs text-gray-500 mb-2">
                Satış fiyatı{{ $soldDate ? ' · Satış tarihi: ' . $soldDate : '' }}
            </p>
        @endif
    @elseif($size === 'small')
        <p {{ $attributes->merge(['class' => 'font-medium text-brand-black100 text-xs mt-1']) }}>{{ $artwork->formatted_price_tl }}</p>
        @if($soldDate)
            <p class="text-gray-400 text-[10px]">Satış: {{ $soldDate }}</p>
        @endif
    @else
        <p class="font-medium text-brand-black100 text-sm">{{ $artwork->formatted_price_tl }}</p>
        <p class="text-gray-400 text-[10px]">{{ $artwork->formatted_price_usd }}{{ $soldDate ? ' · Satış: ' . $soldDate : '' }}</p>
    @endif
@else
    @if($size === 'detail')
        <div class="bg-gray-50 border border-gray-200 px-4 py-3 mb-3 text-sm text-gray-600">
            Satış fiyatı ve satış tarihi yalnızca üyelerimize gösterilir.
            <a href="{{ route('login') }}" class="text-primary font-medium underline">Giriş yapın</a>
            veya <a href="{{ route('register') }}" class="text-primary font-medium underline">ücretsiz üye olun</a>.
        </div>
    @else
        <p class="text-[11px] text-gray-500 leading-snug {{ $size === 'small' ? 'mt-1' : '' }}">Satış fiyatı üyelere özel</p>
    @endif
@endif
