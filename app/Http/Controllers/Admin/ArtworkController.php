<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArtworkController extends Controller
{
    public function index()
    {
        $artworks = Artwork::with('artist', 'category')
            ->latest()
            ->paginate(20);

        return view('admin.artworks.index', compact('artworks'));
    }

    public function create()
    {
        $artists = Artist::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.artworks.create', compact('artists', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'required|exists:artists,id',
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'technique' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'price_tl' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'is_sold' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'nullable|image|max:4096',
        ]);

        $validated['slug'] = Str::slug($validated['title'] . '-' . uniqid());

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('artworks', 'public');
            }
            $validated['images'] = $images;
        }

        Artwork::create($validated);

        return redirect()->route('admin.artworks.index')
            ->with('success', 'Eser başarıyla eklendi.');
    }

    public function edit(Artwork $artwork)
    {
        $artists = Artist::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.artworks.edit', compact('artwork', 'artists', 'categories'));
    }

    public function update(Request $request, Artwork $artwork)
    {
        $validated = $request->validate([
            'artist_id' => 'required|exists:artists,id',
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'technique' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'price_tl' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'is_sold' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('images')) {
            $images = $artwork->images ?? [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('artworks', 'public');
            }
            $validated['images'] = $images;
        }

        $artwork->update($validated);

        return redirect()->route('admin.artworks.index')
            ->with('success', 'Eser başarıyla güncellendi.');
    }

    public function destroy(Artwork $artwork)
    {
        $artwork->delete();

        return redirect()->route('admin.artworks.index')
            ->with('success', 'Eser başarıyla silindi.');
    }
}
