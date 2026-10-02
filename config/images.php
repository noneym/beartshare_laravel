<?php

return [
    /*
    | Thumbor görsel sunucusu. Boş bırakılırsa URL'ler diskin kendi URL'inden
    | (örn. APP_URL/storage/...) üretilir, boyutlandırma yapılmaz.
    */
    'thumbor_url' => rtrim((string) env('THUMBOR_URL', ''), '/'),

    // Thumbor SECURITY_KEY. Doluysa URL'ler HMAC-SHA1 ile imzalanır.
    'thumbor_key' => env('THUMBOR_KEY'),

    // true ise /unsafe/ URL'leri üretilir (Thumbor'da ALLOW_UNSAFE_URL=True olmalı).
    'thumbor_unsafe' => (bool) env('THUMBOR_UNSAFE', false),

    // Varsayılan filtreler (ör. kalite ve format)
    'default_filters' => ['quality(85)'],

    // Önceden tanımlı boyutlar: [genişlik, yükseklik] (0 = oranı koru)
    'presets' => [
        'thumb'  => [400, 0],
        'card'   => [900, 0],
        'detail' => [1600, 0],
        'avatar' => [320, 320],
        'blog'   => [1200, 0],
    ],
];
