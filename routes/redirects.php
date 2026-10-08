<?php

/*
| Eski siteden (beartshare-client, Nuxt) kalan adreslerin yeni karşılıklarına kalıcı (301) yönlendirmeleri.
| Sorgu parametreleri taşınmaz. Eser adreslerinin sonundaki sayı eserin id'sidir: /eserler/duvar-53
|
| Sabit hedefler Route::permanentRedirect ile tanımlanır (closure yok): route:cache, aynı satırda
| iç içe closure'ları (fn () => fn () => ...) doğru serileştiremiyor ve canlıda 500 veriyordu.
*/

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtTerm;
use Illuminate\Support\Facades\Route;

// Eser: /eserler/{isim}-{id}, /second-market/{isim}-{id}, /shared-artworks/{isim}-{id}
$artwork = function (string $path) {
    $id = (int) \Illuminate\Support\Str::afterLast($path, '-');
    $slug = $id ? Artwork::where('id', $id)->where('is_active', true)->value('slug') : null;
    return $slug ? redirect()->route('artwork.detail', $slug, 301) : redirect()->route('artworks', [], 301);
};
Route::get('/eserler/{path}', $artwork)->where('path', '.+');
Route::get('/second-market/{path}', $artwork)->where('path', '.+');
Route::get('/shared-artworks/{path}', $artwork)->where('path', '.+');
Route::permanentRedirect('/second-market', '/eserler');
Route::permanentRedirect('/shared-artworks', '/eserler');

// Sanatçı: çoğunun slug'ı aynı; farklı olanlar legacy_slug ile bulunur
Route::permanentRedirect('/artists', '/sanatcilar');
Route::get('/artists/{slug}', function (string $slug) {
    $slug = \Illuminate\Support\Str::before($slug, '/');
    $target = Artist::where('slug', $slug)->value('slug') ?? Artist::where('legacy_slug', $slug)->value('slug');
    return $target
        ? redirect()->route('artist.detail', $target, 301)
        : redirect()->route('artists', [], 301);
})->where('slug', '.+');

// Sanat haberleri: yazı slug'ları aynı
Route::permanentRedirect('/art-market-news', '/blog');
Route::get('/art-market-news/{slug}', function (string $slug) {
    return redirect()->route('blog.detail', \Illuminate\Support\Str::before($slug, '/'), 301);
})->where('slug', '.+');

// Sanat terimleri: slug eski sitenin slugify'ıyla üretildi
Route::permanentRedirect('/art-terms', '/sanat-terimleri');
Route::get('/art-terms/{slug}', function (string $slug) {
    $slug = \Illuminate\Support\Str::before($slug, '/');
    return ArtTerm::where('slug', $slug)->exists()
        ? redirect()->route('art-terms.show', $slug, 301)
        : redirect()->route('art-terms', [], 301);
})->where('slug', '.+');

// Sayfalar
Route::permanentRedirect('/about', '/hakkimizda');
Route::permanentRedirect('/how-it-works', '/hakkimizda');
Route::permanentRedirect('/profit', '/hakkimizda');
Route::permanentRedirect('/testimonials', '/');
Route::permanentRedirect('/loyalty', '/artpuan');
Route::permanentRedirect('/artwork-acceptance', '/eser-kabulu');
Route::permanentRedirect('/banka', '/banka-hesaplari');
Route::permanentRedirect('/contact-us', '/iletisim');
Route::permanentRedirect('/faq', '/sikca-sorulan-sorular');
Route::permanentRedirect('/privacy-policy', '/gizlilik-ve-kvkk');
Route::permanentRedirect('/kvkk', '/gizlilik-ve-kvkk');
Route::permanentRedirect('/delivery-return-policy', '/teslimat-ve-iade');
Route::permanentRedirect('/distance-sale-agreement', '/mesafeli-satis-sozlesmesi');
Route::permanentRedirect('/toc', '/kullanim-kosullari');

// Üyelik, hesap, sepet
Route::permanentRedirect('/login', '/giris');
Route::permanentRedirect('/auth/login', '/giris');
Route::permanentRedirect('/register', '/kayit');
Route::permanentRedirect('/auth/register', '/kayit');
Route::permanentRedirect('/auth/forgot-password', '/sifremi-unuttum');
Route::permanentRedirect('/auth/reset-password', '/sifremi-unuttum');
Route::permanentRedirect('/basket', '/sepet');
Route::permanentRedirect('/checkout', '/sepet');
Route::permanentRedirect('/account', '/hesabim/settings');
Route::permanentRedirect('/account/information', '/hesabim/settings');
Route::permanentRedirect('/account/profile', '/hesabim/settings');
Route::permanentRedirect('/account/settings', '/hesabim/settings');
Route::permanentRedirect('/account/orders/{any?}', '/hesabim/orders')->where('any', '.*');
Route::permanentRedirect('/order/{any}', '/hesabim/orders')->where('any', '.*');
Route::permanentRedirect('/account/favorites', '/favorilerim');
Route::permanentRedirect('/account/addresses/{any?}', '/adreslerim')->where('any', '.*');
Route::permanentRedirect('/account/loyalty', '/hesabim/artpuan');
Route::permanentRedirect('/account/{any}', '/hesabim')->where('any', '.*');
