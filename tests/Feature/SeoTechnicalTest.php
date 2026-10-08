<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Teknik SEO: sonu eğik çizgili adresler, görsel sitemap, kırıntı şeması, preload.
 */
class SeoTechnicalTest extends TestCase
{
    public function test_trailing_slash_redirects_permanently_and_keeps_query(): void
    {
        // $this->get() adresi kendisi kırptığı için istek doğrudan kernel'e verilir
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

        $res = $kernel->handle(Request::create('/eserler/', 'GET'));
        $this->assertSame(301, $res->getStatusCode());
        $this->assertSame(url('/eserler'), $res->headers->get('Location'));

        $res = $kernel->handle(Request::create('/eserler/?page=2', 'GET'));
        $this->assertSame(301, $res->getStatusCode());
        $this->assertSame(url('/eserler?page=2'), $res->headers->get('Location'));

        $this->assertSame(200, $kernel->handle(Request::create('/', 'GET'))->getStatusCode());
    }

    public function test_sitemap_contains_artwork_images(): void
    {
        Cache::forget('sitemap.xml');

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"', false)
            ->assertSee('<image:loc>', false)
            ->assertDontSee('format(webp)', false);

        Cache::forget('sitemap.xml');
    }

    public function test_home_preloads_hero_and_preconnects_image_host(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('rel="preload" as="image"', false)
            ->assertSee('imagesizes="100vw"', false)
            ->assertSee('<title>BeArtShare | Yeni Çağın Online Sanat Galerisi</title>', false);
    }

    public function test_artwork_and_artist_pages_have_breadcrumb_schema(): void
    {
        // Satışta olan eser: iade/kargo bilgisi yalnızca teklif (offers) varken basılır
        $artwork = Artwork::available()->with('artist')->first();
        $artist = Artist::active()->first();

        if (! $artwork || ! $artist) {
            $this->markTestSkipped('Veritabanında eser/sanatçı yok.');
        }

        $this->get('/eser/' . $artwork->slug)
            ->assertOk()
            ->assertSee('"@graph":[{"@type":"Product"', false)
            ->assertSee('"@type":"BreadcrumbList"', false)
            ->assertSee('"@type":"MerchantReturnPolicy"', false);

        $this->get('/sanatci/' . $artist->slug)
            ->assertOk()
            ->assertSee('"@graph":[{"@type":"Person"', false)
            ->assertSee('"name":"Sanatçılar"', false);
    }
}
