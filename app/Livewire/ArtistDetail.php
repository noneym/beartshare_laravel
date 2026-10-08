<?php

namespace App\Livewire;

use App\Models\Artist;
use Illuminate\Support\Str;
use App\Livewire\Concerns\PaginatedSeo;
use Livewire\Component;
use Livewire\WithPagination;

class ArtistDetail extends Component
{
    use WithPagination, PaginatedSeo;

    public Artist $artist;

    public function mount($slug)
    {
        $this->artist = Artist::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        $artworks = $this->artist->artworks()
            ->where('is_active', true)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $artist = $this->artist;
        $artworkCount = $this->artist->artworks()->where('is_active', true)->count();
        $biography = $artist->biography
            ? Str::limit(strip_tags(html_entity_decode($artist->biography, ENT_QUOTES, 'UTF-8')), 160)
            : "{$artist->name} sanatçısının orijinal eserleri BeArtShare'de. {$artworkCount} eser mevcut.";

        $imageUrl = $artist->avatar_url ?? asset('images/og-default.jpg');
        // Paylaşım görseli: 320px avatar yerine 1200x630 akıllı kırpım; fotoğraf yoksa site varsayılanı
        $ogImage = \App\Support\ImageUrl::make($artist->avatar ?: $artist->image, 1200, 630) ?? asset('images/og-default.jpg');

        $jsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $artist->name,
            'description' => $biography,
            'image' => $imageUrl,
            'url' => url()->current(),
            'jobTitle' => 'Sanatçı',
            'memberOf' => [
                '@type' => 'Organization',
                'name' => 'BeArtShare',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return view('livewire.artist-detail', [
            'artworks' => $artworks,
        ])->layoutData([
            'title' => $this->paginatedTitle("{$artist->name} - Sanatçı Profili | BeArtShare"),
            'canonical' => $this->paginatedCanonical(),
            'metaDescription' => $biography,
            'metaKeywords' => implode(', ', [$artist->name, 'sanatçı', 'eserler', 'orijinal tablo', 'beartshare']),
            'ogType' => 'profile',
            'ogTitle' => "{$artist->name} | BeArtShare Sanatçı",
            'ogDescription' => $biography,
            'ogImage' => $ogImage,
            'jsonLd' => $jsonLd,
        ]);
    }
}
