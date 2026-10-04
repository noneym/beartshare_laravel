<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SortsIndex;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    use SortsIndex;

    /**
     * TinyMCE editöründen gelen görseli R2'ye yükler, Thumbor URL'i döner.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif,avif', 'max:15360'],
        ], [
            'image.max' => 'Görsel en fazla 15 MB olabilir.',
            'image.mimes' => 'Sadece JPG, PNG, WEBP, GIF veya AVIF yükleyebilirsiniz.',
        ]);

        $file = $request->file('image');
        $name = date('Ymd') . '-' . Str::random(12) . '.' . strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $path = $file->storeAs('blog/content', $name, config('filesystems.uploads'));

        if (!$path) {
            return response()->json(['message' => 'Görsel kaydedilemedi.'], 500);
        }

        return response()->json([
            'location' => \App\Support\ImageUrl::make($path, 'blog'),
            'path' => $path,
        ]);
    }

    public function index(Request $request)
    {
        $query = BlogPost::with('category', 'user')
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
            });

        // Başlık sıralaması (sort + dir) yoksa mevcut sıralama seçimi / varsayılan
        $sorted = $this->applySort($query, $request, [
            'id' => 'blog_posts.id',
            'title' => 'blog_posts.title',
            'category' => fn ($q, $dir) => $q->orderBy(
                BlogCategory::select('title')->whereColumn('blog_categories.id', 'blog_posts.blog_category_id'),
                $dir
            ),
            'author' => fn ($q, $dir) => $q->orderBy(
                User::select('name')->whereColumn('users.id', 'blog_posts.user_id'),
                $dir
            ),
            'status' => 'blog_posts.is_active',
            'date' => 'blog_posts.created_at',
        ]);

        if (!$sorted) {
            $query->when($request->filled('sort'), function ($query) use ($request) {
                match ($request->input('sort')) {
                    'oldest' => $query->oldest(),
                    'title' => $query->orderBy('title', 'asc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            });
        }

        $posts = $query->paginate(20)->withQueryString();

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
            $validated['image'] = $request->file('image')->store('blog', config('filesystems.uploads'));
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
            $validated['image'] = $request->file('image')->store('blog', config('filesystems.uploads'));
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
