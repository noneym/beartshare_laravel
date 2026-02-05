<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Artwork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Live search - eser ve sanatçı ara
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['artworks' => [], 'artists' => []]);
        }

        // Eserleri ara (max 5)
        $artworks = Artwork::with('artist')
            ->available()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhereHas('artist', function ($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%");
                  });
            })
            ->limit(5)
            ->get()
            ->map(function ($artwork) {
                return [
                    'id' => $artwork->id,
                    'title' => $artwork->title,
                    'artist' => $artwork->artist->name ?? '',
                    'price' => number_format($artwork->price_tl, 0, ',', '.') . ' TL',
                    'image' => $artwork->first_image_url,
                    'url' => route('artwork.detail', $artwork->slug),
                ];
            });

        // Sanatçıları ara (max 3)
        $artists = Artist::active()
            ->where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(function ($artist) {
                return [
                    'id' => $artist->id,
                    'name' => $artist->name,
                    'image' => $artist->avatar_url,
                    'url' => route('artist.detail', $artist->slug),
                    'artwork_count' => $artist->artworks()->available()->count(),
                ];
            });

        return response()->json([
            'artworks' => $artworks,
            'artists' => $artists,
        ]);
    }
}
