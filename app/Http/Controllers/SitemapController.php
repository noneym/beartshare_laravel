<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtTerm;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Cache;

/**
 * /sitemap.xml: sabit sayfalar, eserler, sanatçılar, blog yazıları ve sanat terimleri. 1 saat önbellekte.
 */
class SitemapController extends Controller
{
    public function __invoke()
    {
        $xml = Cache::remember('sitemap.xml', 3600, function () {
            $urls = [];
            $add = function (string $loc, $lastmod = null, string $freq = 'weekly', string $priority = '0.5') use (&$urls) {
                $urls[] = compact('loc', 'lastmod', 'freq', 'priority');
            };

            $add(route('home'), null, 'daily', '1.0');
            $add(route('artworks'), null, 'daily', '0.9');
            $add(route('artists'), null, 'weekly', '0.8');
            $add(route('exhibition.3d'), null, 'weekly', '0.6');
            $add(route('blog'), null, 'weekly', '0.6');
            $add(route('art-terms'), null, 'monthly', '0.5');
            foreach (['about', 'artpuan', 'eser-kabulu', 'contact', 'faq', 'banka-hesaplari', 'teslimat-iade',
                      'gizlilik-kvkk', 'kullanim-kosullari', 'mesafeli-satis'] as $name) {
                $add(route($name), null, 'monthly', '0.4');
            }

            foreach (Artwork::where('is_active', true)->get(['slug', 'updated_at', 'is_sold']) as $a) {
                $add(route('artwork.detail', $a->slug), $a->updated_at, 'weekly', $a->is_sold ? '0.5' : '0.8');
            }
            foreach (Artist::active()->get(['slug', 'updated_at']) as $a) {
                $add(route('artist.detail', $a->slug), $a->updated_at, 'weekly', '0.7');
            }
            foreach (BlogPost::where('is_active', true)->get(['slug', 'updated_at']) as $p) {
                $add(route('blog.detail', $p->slug), $p->updated_at, 'monthly', '0.5');
            }
            foreach (ArtTerm::active()->get(['slug', 'updated_at']) as $t) {
                $add(route('art-terms.show', $t->slug), $t->updated_at, 'yearly', '0.3');
            }

            $out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($urls as $u) {
                $out .= '  <url><loc>' . e($u['loc']) . '</loc>'
                    . ($u['lastmod'] ? '<lastmod>' . $u['lastmod']->toAtomString() . '</lastmod>' : '')
                    . '<changefreq>' . $u['freq'] . '</changefreq><priority>' . $u['priority'] . '</priority></url>' . "\n";
            }
            return $out . '</urlset>';
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
