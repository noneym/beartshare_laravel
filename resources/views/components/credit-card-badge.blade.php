@props(['artwork'])
@if($artwork->allow_credit_card && !$artwork->is_sold)
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 text-[11px] font-medium text-gray-600 border border-gray-200 px-1.5 py-0.5 mt-1 whitespace-nowrap']) }}>
    <i class="ph ph-credit-card text-sm"></i>
    Kredi Kartı
</span>
@endif
