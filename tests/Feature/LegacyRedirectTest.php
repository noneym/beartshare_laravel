<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Eski site (Nuxt) adreslerinin 301 ile yeni karşılıklarına gitmesi.
 * Veritabanı gerektirmeyen sabit yönlendirmeler (routes/redirects.php).
 */
class LegacyRedirectTest extends TestCase
{
    public static function staticRedirects(): array
    {
        return [
            ['/about', '/hakkimizda'],
            ['/artists', '/sanatcilar'],
            ['/art-market-news', '/blog'],
            ['/art-terms', '/sanat-terimleri'],
            ['/second-market', '/eserler'],
            ['/faq', '/sikca-sorulan-sorular'],
            ['/kvkk', '/gizlilik-ve-kvkk'],
            ['/login', '/giris'],
            ['/auth/register', '/kayit'],
            ['/basket', '/sepet'],
            ['/account', '/hesabim/settings'],
            ['/account/orders/123/x', '/hesabim/orders'],
            ['/account/addresses/5', '/adreslerim'],
            ['/account/loyalty', '/hesabim/artpuan'],
            ['/account/anything/else', '/hesabim'],
        ];
    }

    /** @dataProvider staticRedirects */
    public function test_legacy_url_permanently_redirects(string $from, string $to): void
    {
        $this->get($from)->assertStatus(301)->assertRedirect($to);
    }
}
