<?php

namespace App\Livewire;

use App\Models\Artist;
use Livewire\Component;
use Livewire\WithPagination;

class ArtistList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $artists = Artist::active()
            ->withCount('artworks')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.artist-list', [
            'artists' => $artists,
        ])->layoutData([
            'title' => 'Sanatçılar | BeArtShare - Türk ve Uluslararası Sanatçılar',
            'metaDescription' => 'BeArtShare\'de Türkiye\'nin ve dünyanın en değerli sanatçılarını keşfedin. Sanatçı profilleri, biyografileri ve orijinal eserleri.',
            'metaKeywords' => 'türk sanatçılar, çağdaş sanatçılar, ressam, heykeltıraş, sanatçı profilleri, sanat eserleri',
            'ogType' => 'website',
        ]);
    }
}
