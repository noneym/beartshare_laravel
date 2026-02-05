<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::with('category', 'user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('id', $search);
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                match ($request->input('status')) {
                    'active' => $query->where('is_active', true),
                    'passive' => $query->where('is_active', false),
                    default => $query,
                };
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('blog_category_id', $request->input('category_id'));
            })
            ->when($request->filled('sort'), function ($query) use ($request) {
                match ($request->input('sort')) {
                    'oldest' => $query->oldest(),
                    'title' => $query->orderBy('title', 'asc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(20)
            ->withQueryString();

        $categories = BlogCategory::orderBy('title')->get();

        return view('admin.blog-posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = BlogCategory::active()->orderBy('title')->get();

        return view('admin.blog-posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'content' => 'nullable|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'image' => 'nullable|image|max:4096',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'Yazi basligi zorunludur.',
            'slug.unique' => 'Bu slug zaten kullaniliyor.',
            'image.image' => 'Gorsel bir resim dosyasi olmalidir.',
            'image.max' => 'Gorsel en fazla 4MB olabilir.',
        ]);

        $validated['slug'] = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        $validated['is_active'] = $request->has('is_active');
        $validated['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blog', 'public');
        }

        BlogPost::create($validated);

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog yazisi basariyla olusturuldu.');
    }

    public function edit(BlogPost $blogPost)
    {
        $categories = BlogCategory::active()->orderBy('title')->get();

        return view('admin.blog-posts.edit', compact('blogPost', 'categories'));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $blogPost->id,
            'content' => 'nullable|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'image' => 'nullable|image|max:4096',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'Yazi basligi zorunludur.',
            'slug.unique' => 'Bu slug zaten kullaniliyor.',
            'image.image' => 'Gorsel bir resim dosyasi olmalidir.',
            'image.max' => 'Gorsel en fazla 4MB olabilir.',
        ]);

        $validated['slug'] = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blog', 'public');
        }

        $blogPost->update($validated);

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog yazisi basariyla guncellendi.');
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog yazisi basariyla silindi.');
    }
}
