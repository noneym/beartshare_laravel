<?php

namespace App\Livewire;

use App\Models\Artist;
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

        return view('livewire.artist-detail', [
            'artworks' => $artworks,
        ]);
    }
}
