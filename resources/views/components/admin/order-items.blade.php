@props(['items'])

{{-- Siparişteki eserler: küçük görsel, ad (eser düzenlemeye bağlı), sanatçı, fiyat. Sipariş listelerinde satırın altında. --}}
<div {{ $attributes->merge(['class' => 'flex flex-wrap gap-2']) }}>
    @foreach($items as $item)
        <div class="flex items-center gap-2.5 bg-gray-50 border border-gray-100 rounded-lg pl-1.5 pr-3 py-1.5 max-w-xs">
            @php $thumb = $item->artwork?->imageUrl('thumb'); @endphp
            @if($thumb)
                <img src="{{ $thumb }}" alt="" loading="lazy" class="w-10 h-10 object-cover rounded shrink-0">
            @else
                <div class="w-10 h-10 rounded bg-gray-200 shrink-0"></div>
            @endif
            <div class="min-w-0">
                @if($item->artwork)
                    <a href="{{ route('admin.artworks.edit', $item->artwork_id) }}" class="block text-sm text-gray-900 hover:text-primary truncate" title="{{ $item->artwork_title }}">{{ $item->artwork_title }}</a>
                @else
                    <span class="block text-sm text-gray-900 truncate" title="{{ $item->artwork_title }}">{{ $item->artwork_title }}</span>
                @endif
                <p class="text-xs text-gray-500 truncate">
                    {{ $item->artist_name ?: '—' }} · {{ number_format($item->price_tl, 0, ',', '.') }} TL{{ $item->quantity > 1 ? ' × ' . $item->quantity : '' }}
                </p>
            </div>
        </div>
    @endforeach
</div>
