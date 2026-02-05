<?php

namespace App\Livewire;

use App\Models\Artist;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class ArtistDetail extends Component
{
    use WithPagination;

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
            ->paginate(12);

        $artist = $this->artist;
        $artworkCount = $this->artist->artworks()->where('is_active', true)->count();
        $biography = $artist->biography
            ? Str::limit(strip_tags(html_entity_decode($artist->biography, ENT_QUOTES, 'UTF-8')), 160)
            : "{$artist->name} sanatçısının orijinal eserleri BeArtShare'de. {$artworkCount} eser mevcut.";

        $imageUrl = $artist->avatar ?? $artist->image ?? asset('images/og-default.jpg');
        if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
            $imageUrl = asset('storage/' . $imageUrl);
        }

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
            'title' => "{$artist->name} - Sanatçı Profili | BeArtShare",
            'metaDescription' => $biography,
            'metaKeywords' => implode(', ', [$artist->name, 'sanatçı', 'eserler', 'orijinal tablo', 'beartshare']),
            'ogType' => 'profile',
            'ogTitle' => "{$artist->name} | BeArtShare Sanatçı",
            'ogDescription' => $biography,
            'ogImage' => $imageUrl,
            'jsonLd' => $jsonLd,
        ]);
    }
}
