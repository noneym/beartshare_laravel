<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="robots" content="noindex">
    <title>{{ $page['title'] }} · 3D Galeri | BeArtShare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; overflow: hidden; background: #e9e6e1; font-family: 'Prompt', sans-serif; color: #1a1a1a; }
        #stage { position: fixed; inset: 0; touch-action: none; }
        #stage canvas { display: block; }
        .ui { position: fixed; z-index: 10; }
        .glass { background: rgba(255,255,255,.82); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
        #top { top: 16px; left: 16px; right: 16px; display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; pointer-events: none; }
        #top > * { pointer-events: auto; }
        #top .left { display: flex; gap: 8px; align-items: center; }
        .light-btn { display: inline-flex; align-items: center; gap: 8px; padding: 9px 14px; border: 0; font: inherit; font-size: 14px; color: #777; cursor: pointer; transition: color .3s; }
        .light-btn svg { width: 18px; height: 18px; }
        .light-btn .glow { opacity: 0; transition: opacity .3s; }
        .light-btn.on { color: #1a1a1a; }
        .light-btn.on svg { color: #d99a16; }
        .light-btn.on .glow { opacity: 1; }
        .back { display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; font-size: 14px; text-decoration: none; color: inherit; }
        .tools { display: flex; gap: 6px; padding: 6px; align-items: center; }
        .tools button { border: 0; background: transparent; font: inherit; font-size: 13px; padding: 7px 10px; border-radius: 8px; cursor: pointer; color: inherit; }
        .tools button:hover, .tools button.on { background: rgba(0,0,0,.07); }
        .swatch { width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px rgba(0,0,0,.15); cursor: pointer; padding: 0 !important; }
        .swatch.on { box-shadow: 0 0 0 2px #1a1a1a; }
        .sep { width: 1px; height: 20px; background: rgba(0,0,0,.12); margin: 0 4px; }
        #info { left: 16px; bottom: 16px; max-width: 340px; padding: 16px 18px; transition: opacity .3s, transform .3s; }
        #info.hidden { opacity: 0; transform: translateY(8px); pointer-events: none; }
        #info .artist { font-size: 13px; color: #777; }
        #info h2 { font-weight: 400; font-size: 19px; line-height: 1.3; margin: 2px 0 8px; }
        #info .meta { font-size: 13px; color: #555; line-height: 1.6; }
        #info .row { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; gap: 12px; }
        #info .price { font-size: 16px; font-weight: 500; }
        #info .badge { font-size: 12px; padding: 3px 8px; border-radius: 999px; background: #1a1a1a; color: #fff; }
        #info a.cta { font-size: 13px; padding: 8px 14px; background: #1a1a1a; color: #fff; border-radius: 8px; text-decoration: none; white-space: nowrap; }
        #hint { right: 16px; bottom: 16px; padding: 10px 14px; font-size: 12px; color: #555; line-height: 1.6; }
        #loading { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 20; padding: 24px;
            background: radial-gradient(120% 90% at 50% 40%, #f6f4f0 0%, #e9e6e1 60%, #dcd7cf 100%); transition: opacity .8s ease, transform .8s ease; }
        #loading.done { opacity: 0; transform: scale(1.04); pointer-events: none; }
        .ld { width: min(440px, 100%); text-align: center; }
        .ld-mark { font-weight: 300; font-size: 30px; letter-spacing: .02em; color: #1a1a1a; }
        .ld-sub { font-size: 13px; color: #8a847c; margin-top: 2px; letter-spacing: .08em; text-transform: uppercase; }
        .ld-wall { display: flex; justify-content: center; align-items: flex-end; gap: 10px; height: 92px; margin: 34px 0 30px; }
        .ld-wall.many { flex-wrap: wrap; height: auto; min-height: 92px; align-content: center; align-items: center; gap: 6px; }
        .ld-frame { position: relative; width: 52px; height: 64px; background: #fff; box-shadow: 0 6px 18px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.08);
            overflow: hidden; transition: width .6s cubic-bezier(.2,.8,.2,1), height .6s cubic-bezier(.2,.8,.2,1), transform .6s; animation: ld-float 3s ease-in-out infinite; }
        .ld-frame::before { content: ''; position: absolute; inset: 0; background: linear-gradient(100deg, transparent 20%, rgba(0,0,0,.06) 50%, transparent 80%);
            background-size: 200% 100%; animation: ld-shimmer 1.4s linear infinite; }
        .ld-frame img { position: absolute; inset: 3px; width: calc(100% - 6px); height: calc(100% - 6px); object-fit: cover; opacity: 0; transform: scale(1.15); transition: opacity .6s, transform .9s cubic-bezier(.2,.8,.2,1); }
        .ld-frame.loaded::before { display: none; }
        .ld-frame.loaded img { opacity: 1; transform: none; }
        .ld-frame.main { outline: 1px solid rgba(0,0,0,.18); outline-offset: 4px; }
        .ld-bar { height: 2px; background: rgba(0,0,0,.08); border-radius: 2px; overflow: hidden; }
        .ld-bar span { display: block; height: 100%; width: 0; background: #1a1a1a; border-radius: 2px; }
        .ld-row { display: flex; justify-content: space-between; align-items: baseline; margin-top: 12px; font-size: 13px; color: #555; gap: 12px; }
        #ld-step { text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; animation: ld-in .35s ease; }
        #ld-pct { font-variant-numeric: tabular-nums; font-weight: 500; color: #1a1a1a; }
        #ld-log { list-style: none; margin-top: 18px; font-size: 12px; color: #8a847c; text-align: left; min-height: 60px; }
        #ld-log li { padding: 2px 0; animation: ld-in .4s ease; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        #ld-log li::before { content: '✓'; color: #4a7a57; margin-right: 8px; }
        #ld-log li:nth-child(n+2) { opacity: .6; }
        #ld-log li:nth-child(n+3) { opacity: .3; }
        @keyframes ld-shimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }
        @keyframes ld-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
        @keyframes ld-in { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
        @media (prefers-reduced-motion: reduce) { .ld-frame, .ld-frame::before { animation: none; } }
        @media (max-width: 640px) {
            .back span, .light-btn span, #hint .desk, .tools .label { display: none; }
            #info { right: 16px; max-width: none; bottom: 16px; }
            #hint { display: none; }
        }
        @media (min-width: 641px) { #hint .mob { display: none; } }
    </style>
    @vite('resources/js/gallery3d.js')
</head>
<body>
    <div id="stage"></div>

    <div id="top" class="ui">
        <div class="left">
            <a href="{{ $page['backUrl'] }}" class="glass back">← <span>{{ $page['backLabel'] }}</span></a>
            <button id="btn-lights" class="glass light-btn on" type="button" title="Genel aydınlatmayı aç/kapat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path class="glow" d="M12 2.5v1.5M4.6 5.6l1 1M19.4 5.6l-1 1M2.5 12H4M20 12h1.5"/><path d="M9 18h6M10 21h4M8.2 14.5A5.5 5.5 0 1 1 15.8 14.5c-.8.8-1.3 1.7-1.3 2.5V18h-5v-1c0-.8-.5-1.7-1.3-2.5Z"/></svg>
                <span>Genel ışık</span>
            </button>
        </div>
        <div class="glass tools">
            <button id="btn-focus" title="Ana esere git">⌖ <span class="label">Esere odaklan</span></button>
            <div class="sep"></div>
            <button class="swatch on" data-wall="#f4f2ee" style="background:#f4f2ee" title="Beyaz duvar"></button>
            <button class="swatch" data-wall="#8f9a8b" style="background:#8f9a8b" title="Adaçayı"></button>
            <button class="swatch" data-wall="#3a3d42" style="background:#3a3d42" title="Antrasit"></button>
        </div>
    </div>

    <div id="info" class="ui glass hidden">
        <div class="artist" data-f="artist"></div>
        <h2 data-f="title"></h2>
        <div class="meta" data-f="meta"></div>
        <div class="row">
            <div data-f="price"></div>
            <a class="cta" data-f="url" href="#">Eseri incele</a>
        </div>
    </div>

    <div id="hint" class="ui glass">
        <div class="desk">Sürükle: etrafa bak · Tıkla: oraya yürü<br>Esere tıkla: yaklaş · WASD / oklar: yürü<br>Duvardaki anahtar: eserin ışığını aç/kapa</div>
    </div>

    <div id="loading">
        <div class="ld">
            <div class="ld-mark">BeArtShare</div>
            <div class="ld-sub">{{ $payload['subtitle'] ?? $payload['artist'] }} · Sanal Galeri</div>
            <div class="ld-wall" id="ld-wall"></div>
            <div class="ld-bar"><span id="ld-bar"></span></div>
            <div class="ld-row"><span id="ld-step">Galeri açılıyor…</span><span id="ld-pct">0%</span></div>
            <ul id="ld-log"></ul>
        </div>
    </div>

    <script>window.GALLERY = @json($payload);</script>
</body>
</html>
