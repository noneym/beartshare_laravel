{{--
    Favicon bağlantıları. ?v= dosya değişme zamanıdır: ikon güncellenince tarayıcı önbelleği atlanır.
    Admin panel altın zeminli ayrı ikon kullanır: @include('partials.favicon', ['admin' => true])
--}}
@php
    $suffix = ($admin ?? false) ? '-admin' : '';
    $v = fn (string $file) => asset($file) . '?v=' . (@filemtime(public_path($file)) ?: '1');
@endphp
<link rel="icon" type="image/svg+xml" href="{{ $v("favicon{$suffix}.svg") }}">
<link rel="icon" type="image/x-icon" sizes="16x16 32x32 48x48" href="{{ $v("favicon{$suffix}.ico") }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ $v("apple-touch-icon{$suffix}.png") }}">
<meta name="theme-color" content="{{ ($admin ?? false) ? '#D4A017' : '#221F1F' }}">
