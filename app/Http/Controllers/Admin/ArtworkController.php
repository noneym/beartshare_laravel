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
    public function index(Request $request)
    {
        $artworks = Artwork::with('artist', 'category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('id', $search)
                      ->orWhereHas('artist', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                match ($request->input('status')) {
                    'active' => $query->where('is_active', true)->where('is_sold', false),
                    'passive' => $query->where('is_active', false),
                    'sold' => $query->where('is_sold', true),
                    'featured' => $query->where('is_featured', true),
                    'reserved' => $query->where('is_reserved', true),
                    default => $query,
                };
            })
            ->when($request->filled('artist_id'), function ($query) use ($request) {
                $query->where('artist_id', $request->input('artist_id'));
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->input('category_id'));
            })
            ->when($request->filled('price_range'), function ($query) use ($request) {
                match ($request->input('price_range')) {
                    'under_50k' => $query->where('price_tl', '<', 50000),
                    '50k_100k' => $query->whereBetween('price_tl', [50000, 100000]),
                    '100k_500k' => $query->whereBetween('price_tl', [100000, 500000]),
                    '500k_1m' => $query->whereBetween('price_tl', [500000, 1000000]),
                    'over_1m' => $query->where('price_tl', '>', 1000000),
                    default => $query,
                };
            })
            ->when($request->filled('sort'), function ($query) use ($request) {
                match ($request->input('sort')) {
                    'oldest' => $query->oldest(),
                    'price_asc' => $query->orderBy('price_tl', 'asc'),
                    'price_desc' => $query->orderBy('price_tl', 'desc'),
                    'title' => $query->orderBy('title', 'asc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(20)
            ->withQueryString();

        $artists = Artist::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.artworks.index', compact('artworks', 'artists', 'categories'));
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
            'allow_credit_card' => 'boolean',
            'images.*' => 'nullable|image|max:4096',
        ]);

        $validated['slug'] = Str::slug($validated['title'] . '-' . uniqid());
        $validated['allow_credit_card'] = $request->boolean('allow_credit_card');

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
        $favoritedBy = $artwork->favoritedBy()->latest('favorites.created_at')->get();

        return view('admin.artworks.edit', compact('artwork', 'artists', 'categories', 'favoritedBy'));
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
            'allow_credit_card' => 'boolean',
            'images.*' => 'nullable|image|max:4096',
        ]);

        $validated['allow_credit_card'] = $request->boolean('allow_credit_card');

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
