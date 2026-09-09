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
    public $sortBy = 'latest';
    public $soldFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'artistId' => ['except' => ''],
        'categoryId' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
        'soldFilter' => ['except' => '', 'as' => 'satilanlar'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSoldFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Artwork::with('artist')->where('is_active', true);

        if ($this->soldFilter === 'only' || $this->soldFilter === '1') {
            $query->where('is_sold', true);
        } elseif ($this->soldFilter === 'hide') {
            $query->where('is_sold', false);
        }

        $artworks = $query
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
        ])->layoutData([
            'title' => 'Sanat Eserleri | BeArtShare - Online Sanat Galerisi',
            'metaDescription' => 'BeArtShare online sanat galerisinde yağlı boya tablolar, heykeller, baskılar ve daha fazlasını keşfedin. Orijinal sanat eserlerini güvenle satın alın.',
            'metaKeywords' => 'sanat eserleri, tablo satın al, yağlı boya, akrilik, heykel, baskı, orijinal eser, sanat galerisi',
            'ogType' => 'website',
        ]);
    }
}
