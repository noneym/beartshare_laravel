@props(['artwork', 'aspect' => 'aspect-[4/3]', 'size' => 'md'])
@php
    $titleClass = match($size) { 'lg' => 'text-base', 'sm' => 'text-sm', default => 'text-sm' };
    $priceClass = match($size) { 'lg' => 'text-base', 'sm' => 'text-sm', default => 'text-sm' };
@endphp
<div class="group">
    <a href="{{ route('artwork.detail', $artwork->slug) }}" class="block">
        <div class="relative bg-gray-100 overflow-hidden {{ $aspect }}">
            @if($artwork->is_sold)
                <span class="absolute top-3 left-3 z-10 bg-brand-black100 text-white text-[11px] px-2.5 py-1">Satıldı</span>
            @elseif($artwork->is_reserved)
                <span class="absolute top-3 left-3 z-10 bg-primary text-brand-black100 text-[11px] px-2.5 py-1">Rezerve</span>
            @endif
            @if($artwork->first_image)
                <img src="{{ $artwork->first_image_url }}" alt="{{ $artwork->title }}, {{ $artwork->artist->name ?? '' }}"
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]" loading="lazy">
            @endif
        </div>
    </a>
    <div class="mt-3 flex items-start justify-between gap-4">
        <div class="min-w-0">
            <h3 class="{{ $titleClass }} font-medium text-brand-black100 truncate">{{ $artwork->artist->name ?? '' }}</h3>
            <p class="text-sm text-gray-500 truncate">{{ $artwork->title }}</p>
            @if($size !== 'sm')
                <p class="text-xs text-gray-400 mt-1">{{ $artwork->technique }}@if($artwork->year), {{ $artwork->year }}@endif @if($artwork->dimensions) · {{ $artwork->dimensions }}@endif</p>
            @endif
        </div>
        <div class="text-right flex-shrink-0">
            <p class="{{ $priceClass }} font-medium text-brand-black100">{{ $artwork->formatted_price_tl }}</p>
            @if($size !== 'sm')
                <p class="text-xs text-gray-400">{{ $artwork->formatted_price_usd }}</p>
            @endif
            <x-credit-card-badge :artwork="$artwork" />
        </div>
    </div>
</div>
