<?php

namespace App\Livewire;

use App\Models\BlogPost;
use Illuminate\Support\Str;
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

        $post = $this->post;
        $description = Str::limit(
            strip_tags(html_entity_decode($post->content, ENT_QUOTES, 'UTF-8')),
            160
        );
        $categoryName = $post->category ? $post->category->title : 'Blog';
        $imageUrl = $post->image_url;
        $publishDate = $post->created_at?->toIso8601String();

        $jsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $description,
            'image' => $imageUrl,
            'datePublished' => $publishDate,
            'dateModified' => $post->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'BeArtShare',
                'url' => config('app.url'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'BeArtShare',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.svg'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url()->current(),
            ],
            'articleSection' => $categoryName,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return view('livewire.blog-detail', [
            'relatedPosts' => $relatedPosts,
        ])->layoutData([
            'title' => "{$post->title} | BeArtShare Blog",
            'metaDescription' => $description,
            'metaKeywords' => implode(', ', array_filter([$post->title, $categoryName, 'sanat blogu', 'beartshare'])),
            'ogType' => 'article',
            'ogTitle' => $post->title,
            'ogDescription' => $description,
            'ogImage' => $imageUrl,
            'jsonLd' => $jsonLd,
        ]);
    }
}
