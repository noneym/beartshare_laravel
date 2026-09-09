@props(['artwork'])
@if($artwork->allow_credit_card && !$artwork->is_sold)
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-100 px-1.5 py-0.5 mt-1 whitespace-nowrap']) }}>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
    Kredi Kartı
</span>
@endif
