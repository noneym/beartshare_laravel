{{-- Loggerxo browser SDK: captures JS errors, console.error/warn, failed requests and page-load timing. Key and host come from LOGGERXO_DSN. --}}
@php
    $lxDsn = (string) config('loggerxo.dsn');
    $lxParts = $lxDsn ? parse_url($lxDsn) : false;
    $lxKey = $lxParts['user'] ?? null;
    $lxEndpoint = $lxParts ? (($lxParts['scheme'] ?? 'https').'://'.$lxParts['host'].(isset($lxParts['port']) ? ':'.$lxParts['port'] : '')) : null;
@endphp
@if ($lxKey && $lxEndpoint)
    <script src="{{ $lxEndpoint }}/sdk/loggerxo.js" data-key="{{ $lxKey }}" data-endpoint="{{ $lxEndpoint }}" data-env="{{ config('loggerxo.environment') }}" @if (config('loggerxo.release')) data-release="{{ config('loggerxo.release') }}" @endif></script>
    @auth
        <script>window.Loggerxo && Loggerxo.setUser({ id: @json((string) auth()->id()) });</script>
    @endauth
@endif
