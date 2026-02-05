<?php

namespace App\Livewire;

use App\Models\BlogPost;
use Livewire\Component;

class BlogDetail extends Component
{
    public BlogPost $post;

    public function mount($slug)
    {
        $this->post = BlogPost::active()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();
    }

    public function render()
    {
        $relatedPosts = BlogPost::active()
            ->where('id', '!=', $this->post->id)
            ->when($this->post->blog_category_id, function ($query) {
                $query->where('blog_category_id', $this->post->blog_category_id);
            })
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.blog-detail', [
            'relatedPosts' => $relatedPosts,
        ])->layout('components.layouts.app');
    }
}
