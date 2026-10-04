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
