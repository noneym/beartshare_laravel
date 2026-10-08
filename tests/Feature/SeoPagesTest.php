<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Sayfa başlıkları, h1, şema, robots ve sitemap kuralları (veritabanı yalnızca okunur).
 */
class SeoPagesTest extends TestCase
{
    public function test_home_has_exactly_one_h1(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('/<h1\b/', $html));
    }

    public function test_faq_page_has_own_title_and_faq_schema(): void
    {
        $this->get('/sikca-sorulan-sorular')
            ->assertOk()
            ->assertSee('<title>Sıkça Sorulan Sorular | BeArtShare</title>', false)
            ->assertSee('"@type":"FAQPage"', false);
    }

    public function test_every_page_carries_organization_schema(): void
    {
        $this->get('/hakkimizda')
            ->assertOk()
            ->assertSee('"@type":"Organization"', false)
            ->assertSee('"legalName":"BeArtShare Online Sanat Galerisi A.Ş."', false)
            ->assertSee('"sameAs":["https://www.instagram.com/beartshare"]', false);
    }

    public function test_google_tag_is_printed_only_when_enabled(): void
    {
        config(['services.gtag.enabled' => false]);
        $this->get('/hakkimizda')->assertDontSee('googletagmanager.com/gtag/js', false);

        config(['services.gtag.enabled' => true, 'services.gtag.ids' => 'AW-16638939279, G-TESTID123']);
        $this->get('/hakkimizda')
            ->assertSee('https://www.googletagmanager.com/gtag/js?id=AW-16638939279', false)
            ->assertSee("gtag('config', 'AW-16638939279');", false)
            ->assertSee("gtag('config', 'G-TESTID123');", false);
    }

    public function test_custom_404_page_is_noindex_and_branded(): void
    {
        $this->get('/olmayan-bir-sayfa-xyz')
            ->assertNotFound()
            ->assertSee('<title>Sayfa Bulunamadı | BeArtShare</title>', false)
            ->assertSee('content="noindex, follow"', false)
            ->assertSee('Eserleri Keşfet');
    }

    public function test_robots_blocks_private_flows_and_search_queries(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /sepet')
            ->assertSee('Disallow: /payment')
            ->assertSee('Disallow: /*?search=')
            ->assertSee('Sitemap: ' . route('sitemap'));
    }

    public function test_sitemap_excludes_noindex_virtual_exhibition(): void
    {
        Cache::forget('sitemap.xml');

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertDontSee('/sanal-sergi', false);

        Cache::forget('sitemap.xml');
    }
}
