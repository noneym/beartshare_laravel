@props(['sort' => null, 'first' => 'desc'])

{{--
    Admin tablo başlığı. sort verilirse başlık tıklanabilir: ilk tıklamada `first` yönünde,
    tekrar tıklayınca ters yönde sıralar (controller: SortsIndex::applySort).
--}}
<th {{ $attributes }}>
    @if($sort)
        @php
            $active = request('sort') === $sort && request()->filled('dir');
            $current = request('dir') === 'asc' ? 'asc' : 'desc';
            $next = $active ? ($current === 'asc' ? 'desc' : 'asc') : $first;
        @endphp
        <a href="{{ request()->fullUrlWithQuery(['sort' => $sort, 'dir' => $next, 'page' => null]) }}"
           class="inline-flex items-center gap-1 whitespace-nowrap hover:text-gray-800 {{ $active ? 'text-gray-800' : '' }}">
            {{ $slot }}
            @if($active)
                <span aria-hidden="true">{{ $current === 'asc' ? '▲' : '▼' }}</span>
            @else
                <span aria-hidden="true" class="text-gray-300">↕</span>
            @endif
        </a>
    @else
        {{ $slot }}
    @endif
</th>
