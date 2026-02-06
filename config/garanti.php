<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Garanti Bankası Virtual POS (GVP) Configuration
    |--------------------------------------------------------------------------
    |
    | OOS (Ortak Ödeme Sayfası) entegrasyonu için gerekli ayarlar
    |
    */

    // Çalışma modu: PROD (production) veya TEST
    'mode' => env('GARANTI_MODE', 'PROD'),

    // API versiyonu (Yeni Garanti BBVA API'de 512 olarak geçiyor)
    'api_version' => '512',

    // Terminal bilgileri
    'terminal_id' => env('GARANTI_TERMINAL_ID', '10401052'),
    'merchant_id' => env('GARANTI_MERCHANT_ID', '3216754'),

    // Kullanıcı bilgileri (OOS için her ikisi de PROVOOS olmalı)
    'terminal_user_id' => env('GARANTI_TERMINAL_USER_ID', 'PROVOOS'),
    'terminal_prov_user_id' => env('GARANTI_TERMINAL_PROV_USER_ID', 'PROVOOS'),

    // Şifreler
    'store_key' => env('GARANTI_STORE_KEY', '55514c62632f3736445473446d45464d78356a45642f5864'),
    'provision_password' => env('GARANTI_PROVISION_PASSWORD', 'Nt38421059*'),

    // Şirket adı
    'company_name' => env('GARANTI_COMPANY_NAME', 'BEARTSHARE'),

    // Para birimi kodu (949 = TRY)
    'currency_code' => '949',

    // 3D Secure endpoint
    'gateway_url' => env('GARANTI_GATEWAY_URL', 'https://sanalposprov.garanti.com.tr/servlet/gt3dengine'),

    // Callback URL'leri (dinamik olarak route'lardan alınacak)
    'success_url' => null, // route('payment.callback') ile set edilecek
    'error_url' => null,   // route('payment.callback') ile set edilecek

    // Dil
    'lang' => 'tr',

    // Varsayılan taksit sayısı (boş = taksitsiz)
    'default_installment' => '',

    // Refresh time (saniye) - OOS sonuç sayfasına yönlendirme süresi
    'refresh_time' => '10',
];
