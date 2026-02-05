<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = BlogCategory::withCount('posts')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->input('search') . '%');
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                match ($request->input('status')) {
                    'active' => $query->where('is_active', true),
                    'passive' => $query->where('is_active', false),
                    default => $query,
                };
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'Kategori adi zorunludur.',
            'slug.unique' => 'Bu slug zaten kullaniliyor.',
        ]);

        $validated['slug'] = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        $validated['is_active'] = $request->has('is_active');

        BlogCategory::create($validated);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog kategorisi basariyla olusturuldu.');
    }

    public function edit(BlogCategory $blogCategory)
    {
        return view('admin.blog-categories.edit', compact('blogCategory'));
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug,' . $blogCategory->id,
            'is_active' => 'boolean',
        ], [
            'title.required' => 'Kategori adi zorunludur.',
            'slug.unique' => 'Bu slug zaten kullaniliyor.',
        ]);

        $validated['slug'] = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        $validated['is_active'] = $request->has('is_active');

        $blogCategory->update($validated);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog kategorisi basariyla guncellendi.');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        if ($blogCategory->posts()->count() > 0) {
            return redirect()->route('admin.blog-categories.index')
                ->with('error', 'Bu kategoride yazilar bulunuyor. Once yazilari baska kategoriye tasiyin veya silin.');
        }

        $blogCategory->delete();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog kategorisi basariyla silindi.');
    }
}
