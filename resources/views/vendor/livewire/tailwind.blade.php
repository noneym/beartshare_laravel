{{--
    Livewire sayfalama (livewire::tailwind yerine). Her sayfa gerçek bir <a href="?page=N"> bağlantısıdır:
    arama motorları 2. ve sonraki sayfalara ulaşır; JS açıkken wire:click.prevent sayfayı yenilemeden değiştirir.
--}}
@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
    : '';

$pageName = $paginator->getPageName();

// 1. sayfanın adresi ?page=1 olmadan verilir (liste sayfasının kendisi).
// Livewire yolu baştaki "/" olmadan döndürür ("sanatci/x"); /sanatci/sanatci/x olmasın diye köke bağlanır.
$href = function (int $page) use ($paginator, $pageName) {
    $url = $paginator->url($page);
    if ($page === 1) {
        $url = preg_replace('/([?&])' . preg_quote($pageName, '/') . '=1(?=&|$)/', '$1', $url);
        $url = rtrim(str_replace('?&', '?', $url), '?&');
    }
    if (! \Illuminate\Support\Str::startsWith($url, ['http://', 'https://', '/'])) {
        $url = '/' . $url;
    }
    return $url;
};

$base = 'relative inline-flex items-center justify-center min-w-[40px] h-10 px-3 text-sm border transition';
$idle = $base . ' border-gray-200 text-gray-600 hover:border-brand-black100 hover:text-brand-black100';
$active = $base . ' border-brand-black100 bg-brand-black100 text-white cursor-default';
$disabled = $base . ' border-gray-100 text-gray-300 cursor-default';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Sayfalama" class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-400 order-2 sm:order-1">
                {{ $paginator->total() }} sonuçtan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} arası gösteriliyor
            </p>

            <ul class="flex items-center gap-1 order-1 sm:order-2">
                {{-- Önceki --}}
                <li>
                    @if ($paginator->onFirstPage())
                        <span class="{{ $disabled }}" aria-disabled="true" aria-label="Önceki sayfa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </span>
                    @else
                        <a href="{{ $href($paginator->currentPage() - 1) }}" rel="prev"
                           wire:click.prevent="previousPage('{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}"
                           class="{{ $idle }}" aria-label="Önceki sayfa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    @endif
                </li>

                {{-- Sayfa numaraları --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li><span class="{{ $disabled }}" aria-hidden="true">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li wire:key="paginator-{{ $pageName }}-page{{ $page }}">
                                @if ($page == $paginator->currentPage())
                                    <span class="{{ $active }}" aria-current="page">{{ $page }}</span>
                                @else
                                    <a href="{{ $href($page) }}"
                                       wire:click.prevent="gotoPage({{ $page }}, '{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                       class="{{ $idle }}" aria-label="{{ $page }}. sayfaya git">{{ $page }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach

                {{-- Sonraki --}}
                <li>
                    @if ($paginator->hasMorePages())
                        <a href="{{ $href($paginator->currentPage() + 1) }}" rel="next"
                           wire:click.prevent="nextPage('{{ $pageName }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}"
                           class="{{ $idle }}" aria-label="Sonraki sayfa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span class="{{ $disabled }}" aria-disabled="true" aria-label="Sonraki sayfa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    @endif
                </li>
            </ul>
        </nav>
    @endif
</div>
