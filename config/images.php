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

    // Varsayılan filtreler (ör. kalite). 85: görünür kayıp yok, dosya boyutu ~%50 küçülür
    'default_filters' => ['quality(85)'],

    // Tarayıcıda <img> ile gösterilen görseller için ek filtreler (ImageUrl::web).
    // E-posta, Open Graph ve ürün feed'i bu filtreyi almaz (webp desteği sınırlı).
    'web_filters' => ['format(webp)'],

    // Önceden tanımlı boyutlar: [genişlik, yükseklik] (0 = oranı koru)
    'presets' => [
        'thumb'  => [400, 0],
        'card'   => [900, 0],
        'detail' => [1600, 0],
        'avatar' => [320, 320],
        'blog'   => [1200, 0],
        'email'  => [1200, 0], // 600px e-posta gövdesi, retina için 2x
        'team'   => [600, 800], // Hakkımızda ekip kartı (3:4, yüz odaklı kırpma)
    ],
];
