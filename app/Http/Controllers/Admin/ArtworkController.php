<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SortsIndex;
use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArtworkController extends Controller
{
    use SortsIndex;

    public function index(Request $request)
    {
        $query = Artwork::with('artist', 'category');

        // Başlık sıralaması (?sort=..&dir=..); uygulanmadıysa açılır listedeki sıralama geçerli
        $headerSorted = $this->applySort($query, $request, [
            'id' => 'id',
            'title' => 'title',
            'artist' => fn ($q, $dir) => $q->orderBy(Artist::select('name')->whereColumn('artists.id', 'artworks.artist_id'), $dir),
            'category' => fn ($q, $dir) => $q->orderBy(Category::select('name')->whereColumn('categories.id', 'artworks.category_id'), $dir),
            'price' => 'price_tl',
            'status' => fn ($q, $dir) => $q->orderBy('is_sold', $dir)->orderBy('is_reserved', $dir)->orderBy('is_active', $dir),
        ]);

        $artworks = $query
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
            ->when(!$headerSorted && $request->filled('sort'), function ($query) use ($request) {
                match ($request->input('sort')) {
                    'oldest' => $query->oldest(),
                    'price_asc' => $query->orderBy('price_tl', 'asc'),
                    'price_desc' => $query->orderBy('price_tl', 'desc'),
                    'title' => $query->orderBy('title', 'asc'),
                    default => $query->latest(),
                };
            }, function ($query) use ($headerSorted) {
                if (!$headerSorted) {
                    $query->latest();
                }
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
            'price_usd' => 'nullable|numeric|min:0',
            'is_sold' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'allow_credit_card' => 'boolean',
            'hide_from_gallery' => 'boolean',
            'owner_name' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string|max:5000',
            'sale_note' => 'nullable|string|max:5000',
            'images.*' => 'nullable|image|max:4096',
            'cover' => 'nullable|image|max:4096',
        ]);

        $validated['slug'] = Str::slug($validated['title'] . '-' . uniqid());
        $validated['allow_credit_card'] = $request->boolean('allow_credit_card');
        foreach (['is_active', 'is_featured', 'is_sold', 'hide_from_gallery'] as $flag) {
            $validated[$flag] = $request->boolean($flag);
        }
        $validated['price_usd'] = $this->usdPrice($validated);

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('artworks', config('filesystems.uploads'));
            }
            $validated['images'] = $images;
        }

        if ($request->hasFile('cover')) {
            $validated['cover_image'] = $request->file('cover')->store('artworks/covers', config('filesystems.uploads'));
        }

        Artwork::create($validated);

        return redirect()->route('admin.artworks.index')
            ->with('success', 'Eser başarıyla eklendi.');
    }

    public function edit(Artwork $artwork)
    {
        // Eserin sanatçısı pasif olsa da seçili görünsün
        $artists = Artist::where(fn ($q) => $q->active()->orWhere('id', $artwork->artist_id))->orderBy('name')->get();
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
            'price_usd' => 'nullable|numeric|min:0',
            'is_sold' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'allow_credit_card' => 'boolean',
            'hide_from_gallery' => 'boolean',
            'owner_name' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string|max:5000',
            'sale_note' => 'nullable|string|max:5000',
            'images.*' => 'nullable|image|max:4096',
            'cover' => 'nullable|image|max:4096',
        ]);

        $validated['allow_credit_card'] = $request->boolean('allow_credit_card');
        // İşaretsiz kutular forma gelmez; yoksa kaldırılan işaret kaydedilmezdi
        foreach (['is_active', 'is_featured', 'is_sold', 'hide_from_gallery'] as $flag) {
            $validated[$flag] = $request->boolean($flag);
        }
        // Satılmış eserin USD fiyatı satış anındaki kurla sabit kalır (TL değişmediyse)
        $keepSaleUsd = $artwork->is_sold && $validated['is_sold'] && (float) $validated['price_tl'] === (float) $artwork->price_tl;
        $validated['price_usd'] = $keepSaleUsd ? $artwork->price_usd : $this->usdPrice($validated);

        // Mevcut görsellerin sırası / silinenler (yalnızca bu eserde olan yollar kabul edilir)
        $images = $artwork->images ?? [];
        if ($request->boolean('images_managed')) {
            $images = array_values(array_intersect((array) $request->input('existing_images', []), $images));
            $validated['images'] = $images;
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('artworks', config('filesystems.uploads'));
            }
            $validated['images'] = $images;
        }

        // Kapak fotoğrafı (isteğe bağlı): yeni yükleme değiştirir, işaretlenirse kaldırılır
        if ($request->hasFile('cover')) {
            $validated['cover_image'] = $request->file('cover')->store('artworks/covers', config('filesystems.uploads'));
        } elseif ($request->boolean('remove_cover')) {
            $validated['cover_image'] = null;
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

    /**
     * USD fiyatı güncel TCMB kurundan hesaplanır (rates:update saatlik günceller);
     * kur henüz yoksa formdaki değer kullanılır.
     */
    protected function usdPrice(array $validated): float
    {
        $rate = ExchangeRate::latestRate('USD');
        if ($rate) {
            return round((float) $validated['price_tl'] / $rate, 2);
        }
        return (float) ($validated['price_usd'] ?? 0);
    }
}
