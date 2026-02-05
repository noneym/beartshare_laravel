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
            ->latest()
            ->take(6)
            ->get();

        $latestArtworks = Artwork::with('artist')
            ->available()
            ->latest()
            ->take(8)
            ->get();

        $blogPosts = BlogPost::active()
            ->with('category')
            ->latest()
            ->take(4)
            ->get();

        $totalArtworks = Artwork::available()->count();
        $totalArtists = Artist::active()->count();

        return view('livewire.home-page', [
            'artists' => $artists,
            'featuredArtworks' => $featuredArtworks,
            'latestArtworks' => $latestArtworks,
            'blogPosts' => $blogPosts,
        ])->layoutData([
            'title' => 'BeArtShare - Yeni Çağın Sanat Galerisi | Online Sanat Eseri Al',
            'metaDescription' => "BeArtShare ile {$totalArtists} sanatçıdan {$totalArtworks}+ orijinal sanat eserine ulaşın. Türkiye'nin güvenilir online sanat galerisi. Tablo, heykel ve daha fazlası.",
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
