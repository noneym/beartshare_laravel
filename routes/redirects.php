<?php

/*
| Eski siteden (beartshare-client, Nuxt) kalan adreslerin yeni karşılıklarına kalıcı (301) yönlendirmeleri.
| Sorgu parametreleri taşınmaz. Eser adreslerinin sonundaki sayı eserin id'sidir: /eserler/duvar-53
*/

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtTerm;
use Illuminate\Support\Facades\Route;

$to = fn (string $route, array $params = []) => fn () => redirect()->route($route, $params, 301);

// Eser: /eserler/{isim}-{id}, /second-market/{isim}-{id}, /shared-artworks/{isim}-{id}
$artwork = function (string $path) {
    $id = (int) \Illuminate\Support\Str::afterLast($path, '-');
    $slug = $id ? Artwork::where('id', $id)->where('is_active', true)->value('slug') : null;
    return $slug ? redirect()->route('artwork.detail', $slug, 301) : redirect()->route('artworks', [], 301);
};
Route::get('/eserler/{path}', $artwork)->where('path', '.+');
Route::get('/second-market/{path}', $artwork)->where('path', '.+');
Route::get('/shared-artworks/{path}', $artwork)->where('path', '.+');
Route::get('/second-market', $to('artworks'));
Route::get('/shared-artworks', $to('artworks'));

// Sanatçı: çoğunun slug'ı aynı; farklı olanlar legacy_slug ile bulunur
Route::get('/artists', $to('artists'));
Route::get('/artists/{slug}', function (string $slug) {
    $slug = \Illuminate\Support\Str::before($slug, '/');
    $target = Artist::where('slug', $slug)->value('slug') ?? Artist::where('legacy_slug', $slug)->value('slug');
    return $target
        ? redirect()->route('artist.detail', $target, 301)
        : redirect()->route('artists', [], 301);
})->where('slug', '.+');

// Sanat haberleri: yazı slug'ları aynı
Route::get('/art-market-news', $to('blog'));
Route::get('/art-market-news/{slug}', fn (string $slug) => redirect()->route('blog.detail', \Illuminate\Support\Str::before($slug, '/'), 301))->where('slug', '.+');

// Sanat terimleri: slug eski sitenin slugify'ıyla üretildi
Route::get('/art-terms', $to('art-terms'));
Route::get('/art-terms/{slug}', function (string $slug) {
    $slug = \Illuminate\Support\Str::before($slug, '/');
    return ArtTerm::where('slug', $slug)->exists()
        ? redirect()->route('art-terms.show', $slug, 301)
        : redirect()->route('art-terms', [], 301);
})->where('slug', '.+');

// Sayfalar
Route::get('/about', $to('about'));
Route::get('/how-it-works', $to('about'));
Route::get('/profit', $to('about'));
Route::get('/testimonials', $to('home'));
Route::get('/loyalty', $to('artpuan'));
Route::get('/artwork-acceptance', $to('eser-kabulu'));
Route::get('/banka', $to('banka-hesaplari'));
Route::get('/contact-us', $to('contact'));
Route::get('/faq', $to('faq'));
Route::get('/privacy-policy', $to('gizlilik-kvkk'));
Route::get('/kvkk', $to('gizlilik-kvkk'));
Route::get('/delivery-return-policy', $to('teslimat-iade'));
Route::get('/distance-sale-agreement', $to('mesafeli-satis'));
Route::get('/toc', $to('kullanim-kosullari'));

// Üyelik, hesap, sepet
Route::get('/login', $to('login'));
Route::get('/auth/login', $to('login'));
Route::get('/register', $to('register'));
Route::get('/auth/register', $to('register'));
Route::get('/auth/forgot-password', $to('password.request'));
Route::get('/auth/reset-password', $to('password.request'));
Route::get('/basket', $to('cart'));
Route::get('/checkout', $to('cart'));
Route::get('/account', $to('profile', ['tab' => 'settings']));
Route::get('/account/information', $to('profile', ['tab' => 'settings']));
Route::get('/account/profile', $to('profile', ['tab' => 'settings']));
Route::get('/account/settings', $to('profile', ['tab' => 'settings']));
Route::get('/account/orders/{any?}', $to('profile', ['tab' => 'orders']))->where('any', '.*');
Route::get('/order/{any}', $to('profile', ['tab' => 'orders']))->where('any', '.*');
Route::get('/account/favorites', $to('favorites'));
Route::get('/account/addresses/{any?}', $to('addresses'))->where('any', '.*');
Route::get('/account/loyalty', $to('profile', ['tab' => 'artpuan']));
Route::get('/account/{any}', $to('profile'))->where('any', '.*');
