<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $query = Favorite::with(['user', 'artwork.artist']);

        // Siralama
        $sort = $request->get('sort', 'latest');

        switch ($sort) {
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

        return view('admin.favorites.index', compact('favorites', 'stats', 'topArtworks'));
    }
}
