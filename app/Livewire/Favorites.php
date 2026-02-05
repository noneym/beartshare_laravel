<?php

namespace App\Livewire;

use App\Models\Favorite;
use Livewire\Component;

class Favorites extends Component
{
    public function removeFavorite($artworkId)
    {
        Favorite::where('user_id', auth()->id())
            ->where('artwork_id', $artworkId)
            ->delete();

        $this->dispatch('toast', message: 'Favorilerden kaldırıldı.', type: 'info');
    }

    public function render()
    {
        $favorites = Favorite::with(['artwork.artist'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('livewire.favorites', [
            'favorites' => $favorites,
        ])->layoutData([
            'title' => 'Favorilerim | BeArtShare',
            'metaDescription' => 'Favori sanat eserlerinizi görüntüleyin.',
            'metaRobots' => 'noindex, nofollow',
        ]);
    }
}
