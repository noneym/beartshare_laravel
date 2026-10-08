<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Eski sistemin görsel deposu; orijinal dosyalar /blob ile alınır
    'cloudflare_images' => [
        'account_id' => env('CLOUDFLARE_ACCOUNT_ID', env('R2_ACCOUNT_ID')),
        'email' => env('CLOUDFLARE_EMAIL'),
        'key' => env('CLOUDFLARE_API_KEY'),
    ],

    // Paraşüt muhasebe API v4 (OAuth2 password grant)
    'parasut' => [
        'client_id' => env('PARASUT_CLIENT_ID'),
        'client_secret' => env('PARASUT_CLIENT_SECRET'),
        'username' => env('PARASUT_USERNAME'),
        'password' => env('PARASUT_PASSWORD'),
        'company_id' => env('PARASUT_COMPANY_ID'),
        'base_url' => env('PARASUT_BASE_URL', 'https://api.parasut.com'),
    ],

    // Google tag (gtag.js): Google Ads dönüşüm etiketi ve GA4 ölçüm kimlikleri (virgülle ayrılır).
    // Google Ads: AW-16638939279 (eski Nuxt sitesinden), GA4: G-GTXDEVZHHJ
    'gtag' => [
        'ids' => env('GTAG_IDS', 'AW-16638939279,G-GTXDEVZHHJ'),
        'enabled' => (bool) env('GTAG_ENABLED', env('APP_ENV') === 'production'),
    ],

    'netgsm' => [
        'username' => env('NETGSM_USERNAME'),
        'password' => env('NETGSM_PASSWORD'),
        'header' => env('NETGSM_HEADER', 'BEARTSHARE'),
    ],

];
