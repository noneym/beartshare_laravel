@props(['title', 'subtitle' => null])
<section {{ $attributes->merge(['class' => 'border-b border-gray-100']) }}>
    <div class="container mx-auto px-4 pt-12 pb-8 md:pt-16 md:pb-10">
        <h1 class="text-3xl md:text-4xl font-semibold tracking-tight text-brand-black100">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-gray-500 text-base mt-2 max-w-[65ch]">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
