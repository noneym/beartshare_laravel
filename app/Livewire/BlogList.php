<?php

namespace App\Livewire;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Livewire\Component;
use Livewire\WithPagination;

class BlogList extends Component
{
    use WithPagination;

    public $selectedCategory = '';
    public $search = '';

    protected $queryString = [
        'selectedCategory' => ['except' => '', 'as' => 'kategori'],
        'search' => ['except' => '', 'as' => 'ara'],
    ];

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $posts = BlogPost::active()
            ->with('category')
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('slug', $this->selectedCategory);
                });
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(12);

        $categories = BlogCategory::active()
            ->withCount(['posts' => function ($q) {
                $q->active();
            }])
            ->having('posts_count', '>', 0)
            ->get();

        return view('livewire.blog-list', [
            'posts' => $posts,
            'categories' => $categories,
        ])->layoutData([
            'title' => 'Sanat Blogu | BeArtShare - Sanat Haberleri ve Yazıları',
            'metaDescription' => 'BeArtShare sanat blogunda güncel sanat haberleri, sanatçı röportajları, koleksiyon tavsiyeleri ve sanat dünyasından son gelişmeleri okuyun.',
            'metaKeywords' => 'sanat blogu, sanat haberleri, sanat yazıları, sanatçı röportajları, sanat dünyası, koleksiyon, sanat piyasası',
            'ogType' => 'blog',
        ]);
    }
}
