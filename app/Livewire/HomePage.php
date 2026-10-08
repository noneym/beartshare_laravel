<?php

namespace App\Livewire;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\BlogPost;
use Livewire\Component;

class HomePage extends Component
{
    public function render()
    {
        $artists = Artist::active()
            ->withCount('artworks')
            ->inRandomOrder()
            ->take(20)
            ->get();

        $featuredArtworks = Artwork::with('artist')
            ->available()
            ->featured()
            ->orderByDesc('featured_weight')
            ->latest()
            ->take(8)
            ->get();

        $latestArtworks = Artwork::with('artist')
            ->available()
            ->latest()
            ->take(8)
            ->get();

        $soldArtworks = Artwork::with('artist')
            ->where('is_active', true)
            ->where('is_sold', true)
            ->orderByDesc('updated_at')
            ->take(12)
            ->get();

        $blogPosts = BlogPost::active()
            ->with('category')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(4)
            ->get();

        $totalArtists = Artist::active()->count();

        return view('livewire.home-page', [
            'artists' => $artists,
            'featuredArtworks' => $featuredArtworks,
            'latestArtworks' => $latestArtworks,
            'soldArtworks' => $soldArtworks,
            'blogPosts' => $blogPosts,
        ])->layoutData([
            'title' => 'BeArtShare | Yeni Çağın Online Sanat Galerisi',
            'metaDescription' => "Türkiye'nin ve dünyanın değerli sanatçılarından orijinal eserler: {$totalArtists} sanatçı, yağlıboya, heykel, baskı. Güvenle satın alın, ArtPuan kazanın.",
            // Hero görseli LCP: şablondaki src/srcset ile birebir aynı olmalı
            'preloadImage' => [
                'src' => \App\Support\ImageUrl::make('site/hero/sanal-sergi-v2.webp', 2880),
                'srcset' => \App\Support\ImageUrl::make('site/hero/sanal-sergi-v2.webp', 1440) . ' 1440w, ' . \App\Support\ImageUrl::make('site/hero/sanal-sergi-v2.webp', 2880) . ' 2880w',
                'sizes' => '100vw',
            ],
            'metaKeywords' => 'online sanat galerisi, sanat eseri satın al, orijinal tablo, türk sanatçılar, yağlı boya tablo, sanat yatırımı, heykel, beartshare',
            'ogType' => 'website',
            'jsonLd' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'BeArtShare',
                'url' => config('app.url'),
                'description' => 'Yeni Çağın Sanat Galerisi - Online sanat eseri satın alma platformu',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => url('/eserler') . '?search={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }
}
