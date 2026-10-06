<?php

return [
    /*
    | Passkey (WebAuthn) ile şifresiz giriş. Passkey'ler oluşturuldukları alan adına
    | (rp_id) kilitlidir: test alan adında oluşturulanlar canlı alan adında çalışmaz.
    | Bu yüzden canlıya (beartshare.com) geçince açılır: PASSKEYS_ENABLED=true
    */
    'enabled' => (bool) env('PASSKEYS_ENABLED', false),

    // Alan adı (www'siz kök alan adı www.beartshare.com'u da kapsar). Boşsa isteğin host'u.
    'rp_id' => env('PASSKEY_RP_ID'),

    'rp_name' => 'BeArtShare',

    // Tarayıcının passkey penceresi için süre (sn)
    'timeout' => 120,

    // Üye başına en fazla passkey
    'max_per_user' => 10,
];
