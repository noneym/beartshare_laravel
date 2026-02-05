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
            ->take(6)
            ->get();

        $blogPosts = BlogPost::active()
            ->with('category')
            ->latest()
            ->take(4)
            ->get();

        return view('livewire.home-page', [
            'artists' => $artists,
            'featuredArtworks' => $featuredArtworks,
            'latestArtworks' => $latestArtworks,
            'blogPosts' => $blogPosts,
        ]);
    }
}
