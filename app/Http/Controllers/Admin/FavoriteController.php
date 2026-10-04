<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SortsIndex;
use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use SortsIndex;

    public function index(Request $request)
    {
        $query = Favorite::with(['user', 'artwork.artist']);

        // Siralama
        $sort = $request->get('sort', 'latest');
        // "Esere göre grupla" ve "Satılmış eserleri gizle" varsayılan açık; formda işaret
        // kaldırılınca gizli alan 0 gönderir (bkz. index.blade.php)
        $grouped = $request->has('group') ? $request->boolean('group') : true;
        $hideSold = $request->has('hide_sold') ? $request->boolean('hide_sold') : true;

        // Başlık sıralaması (?sort=<anahtar>&dir=asc|desc); açılır menü değerleriyle çakışmayan anahtarlar
        $artworkCol = fn (string $col) => fn (Builder $q, string $dir) => $q->orderBy(
            Artwork::select($col)->whereColumn('artworks.id', 'favorites.artwork_id'), $dir
        );
        $artworkLevel = [
            'artwork_title' => $artworkCol('title'),
            'artist' => fn (Builder $q, string $dir) => $q->orderBy(
                Artist::select('artists.name')
                    ->join('artworks', 'artworks.artist_id', '=', 'artists.id')
                    ->whereColumn('artworks.id', 'favorites.artwork_id')
                    ->limit(1),
                $dir
            ),
            'price' => $artworkCol('price_tl'),
            // Satildi > Rezerve > Satilikta
            'status' => function (Builder $q, string $dir) use ($artworkCol) {
                $artworkCol('is_sold')($q, $dir);
                $artworkCol('is_reserved')($q, $dir);
            },
        ];
        $favoriteLevel = [
            'user_name' => fn (Builder $q, string $dir) => $q->orderBy(
                User::select('name')->whereColumn('users.id', 'favorites.user_id'), $dir
            ),
            'created' => 'favorites.created_at',
        ];

        if ($grouped) {
            // Gruplu modda gruplar bozulmasın: eser düzeyindeki sütunlar grupları sıralar (eşitlikte eser ID),
            // favori düzeyindeki sütunlar grup içinde sıralar.
            $sortColumns = [];
            foreach ($artworkLevel as $key => $fn) {
                $sortColumns[$key] = function (Builder $q, string $dir) use ($fn) {
                    $fn($q, $dir);
                    $q->orderBy('favorites.artwork_id');
                };
            }
            foreach ($favoriteLevel as $key => $col) {
                $sortColumns[$key] = function (Builder $q, string $dir) use ($col) {
                    $q->orderBy('favorites.artwork_id');
                    $col instanceof \Closure ? $col($q, $dir) : $q->orderBy($col, $dir);
                };
            }
        } else {
            $sortColumns = $artworkLevel + $favoriteLevel;
        }

        $headerSorted = $this->applySort($query, $request, $sortColumns);

        if ($headerSorted) {
            // Başlık sıralaması uygulandı
        } elseif ($grouped) {
            // Esere göre grupla: aynı eserin favorileri alt alta, grup içinde en yeni üstte
            $query->orderBy('artwork_id', $sort === 'artwork_desc' ? 'desc' : 'asc')->latest();
        } else switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'artwork':
                $query->orderBy('artwork_id', 'asc')->latest();
                break;
            case 'artwork_desc':
                $query->orderBy('artwork_id', 'desc')->latest();
                break;
            case 'user':
                $query->join('users', 'favorites.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'asc')
                      ->select('favorites.*');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        // Kullanici filtresi
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Eser filtresi
        if ($request->filled('artwork_id')) {
            $query->where('artwork_id', $request->artwork_id);
        }

        // Satılmış eserleri gizle
        if ($hideSold) {
            $query->whereHas('artwork', fn ($q) => $q->where('is_sold', false));
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('artwork', function ($aq) use ($search) {
                    $aq->where('title', 'like', "%{$search}%");
                })
                ->orWhereHas('artwork.artist', function ($artq) use ($search) {
                    $artq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $favorites = $query->paginate(30)->withQueryString();

        // Istatistikler
        $stats = [
            'total' => Favorite::count(),
            'users_with_favorites' => Favorite::distinct('user_id')->count('user_id'),
            'artworks_favorited' => Favorite::distinct('artwork_id')->count('artwork_id'),
            'today' => Favorite::whereDate('created_at', today())->count(),
            'this_week' => Favorite::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        // En cok favorilenen eserler (top 5)
        $topArtworks = Favorite::selectRaw('artwork_id, COUNT(*) as count')
            ->groupBy('artwork_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('artwork:id,title')
            ->get();

        $groupCounts = $grouped
            ? Favorite::whereIn('artwork_id', $favorites->pluck('artwork_id')->unique())
                ->selectRaw('artwork_id, COUNT(*) as c')->groupBy('artwork_id')->pluck('c', 'artwork_id')
            : collect();

        return view('admin.favorites.index', compact('favorites', 'stats', 'topArtworks', 'grouped', 'hideSold', 'groupCounts'));
    }

    public function updateNote(Request $request, Favorite $favorite)
    {
        $validated = $request->validate(['admin_note' => ['nullable', 'string', 'max:2000']]);
        $favorite->update(['admin_note' => $validated['admin_note'] ?: null]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'admin_note' => $favorite->admin_note]);
        }

        return back()->with('success', 'Not kaydedildi.');
    }
}
