<?php

namespace App\Livewire;

use App\Models\Artwork;
use App\Models\Artist;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ArtworkList extends Component
{
    use WithPagination;

    public $search = '';
    public $artistId = '';
    public $categoryId = '';
    public $priceRange = '';
    public $sortBy = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'artistId' => ['except' => ''],
        'categoryId' => ['except' => ''],
        'priceRange' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $artworks = Artwork::with('artist')
            ->available()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhereHas('artist', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->artistId, function ($query) {
                $query->where('artist_id', $this->artistId);
            })
            ->when($this->categoryId, function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->when($this->priceRange, function ($query) {
                match ($this->priceRange) {
                    'under_100k' => $query->where('price_tl', '<', 100000),
                    '100k_500k' => $query->whereBetween('price_tl', [100000, 500000]),
                    '500k_1m' => $query->whereBetween('price_tl', [500000, 1000000]),
                    'over_1m' => $query->where('price_tl', '>', 1000000),
                    default => $query,
                };
            })
            ->when($this->sortBy, function ($query) {
                match ($this->sortBy) {
                    'latest' => $query->latest(),
                    'oldest' => $query->oldest(),
                    'price_asc' => $query->orderBy('price_tl', 'asc'),
                    'price_desc' => $query->orderBy('price_tl', 'desc'),
                    'name' => $query->orderBy('title', 'asc'),
                    default => $query->latest(),
                };
            })
            ->paginate(12);

        $artists = Artist::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();

        return view('livewire.artwork-list', [
            'artworks' => $artworks,
            'artists' => $artists,
            'categories' => $categories,
        ]);
    }
}
