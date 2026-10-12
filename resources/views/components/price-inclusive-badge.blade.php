@props(['id' => 'price-all-inclusive', 'compact' => false, 'tipAlign' => 'center'])

{{--
    "Her şey dahil" rozeti: masaüstünde üzerine gelince, mobilde dokununca "vergi ve komisyon dahil" açıklaması.
    tipAlign: center (eser sayfası) | right (sağa hizalı kart) | start (mobilde sola, sm+ sağa hizalı kart)
--}}
@php
    $tipPos = match ($tipAlign) {
        'right' => 'right-0',
        'start' => 'left-0 sm:left-auto sm:right-0',
        default => 'left-1/2 -translate-x-1/2',
    };
    $arrowPos = match ($tipAlign) {
        'right' => 'right-3',
        'start' => 'left-3 sm:left-auto sm:right-3',
        default => 'left-1/2 -translate-x-1/2',
    };
@endphp
<span {{ $attributes->merge(['class' => 'relative inline-block group']) }} x-data="{ open: false }" @click.outside="open = false">
    <button type="button" @click.prevent="open = !open" aria-describedby="{{ $id }}"
            class="inline-flex items-center gap-1 border border-gray-300 text-gray-600 font-medium rounded-full hover:border-brand-black100 hover:text-brand-black100 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-black100/30 transition {{ $compact ? 'text-[9px] px-1.5 py-px' : 'text-[11px] px-2 py-0.5' }}">
        Her şey dahil
        <svg class="{{ $compact ? 'w-2.5 h-2.5' : 'w-3 h-3' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </button>
    <span id="{{ $id }}" role="tooltip"
          :class="open ? 'opacity-100 visible' : ''"
          class="absolute {{ $tipPos }} bottom-full mb-2 w-max max-w-[200px] bg-brand-black100 text-white text-xs leading-snug text-left font-normal px-3 py-2 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition z-20 pointer-events-none">
        Vergi ve komisyon oranları dahil fiyattır.
        <span class="absolute {{ $arrowPos }} top-full w-0 h-0 border-x-4 border-x-transparent border-t-4 border-t-brand-black100"></span>
    </span>
</span>
