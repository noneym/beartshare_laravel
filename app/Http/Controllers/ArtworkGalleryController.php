<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Artwork;
use App\Support\ArtworkDimensions;
use App\Support\ImageUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Deneysel: eseri three.js ile sanal bir galeri odasında gösterir.
 */
class ArtworkGalleryController extends Controller
{
    public function show(string $slug)
    {
        $artwork = Artwork::with('artist')
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $others = $this->artistWorks($artwork->artist_id)
            ->where('id', '!=', $artwork->id)
            ->take(6)
            ->get();

        return $this->render($artwork, $others, [
            'title' => $artwork->title,
            'backUrl' => route('artwork.detail', $artwork->slug),
            'backLabel' => 'Esere dön',
        ]);
    }

    /**
     * Sanatçının sanal sergisi: öne çıkan eser arka duvarda, diğerleri yan duvarlarda.
     */
    public function showArtist(string $slug)
    {
        $artist = Artist::where('slug', $slug)->firstOrFail();
        $works = $this->artistWorks($artist->id)->take(11)->get();
        abort_if($works->isEmpty(), 404);

        return $this->render($works->first(), $works->slice(1), [
            'title' => $artist->name,
            'backUrl' => route('artist.detail', $artist->slug),
            'backLabel' => 'Sanatçıya dön',
        ]);
    }

    /**
     * Sanal Galeri (header'daki menü): satıştaki tüm eserlerin tek salonda sergisi.
     * Eserler sanatçıya göre gruplanır; dokular hafif boyutta yüklenir.
     */
    public function showAll()
    {
        $works = Artwork::with('artist')
            ->available()
            ->whereNotNull('images')
            ->where('images', '!=', '[]')
            ->get()
            ->filter(fn ($a) => $a->first_image)
            ->sortBy([
                fn ($a, $b) => strcmp(mb_strtolower($a->artist->name ?? ''), mb_strtolower($b->artist->name ?? '')),
                fn ($a, $b) => $b->is_featured <=> $a->is_featured,
            ])
            ->values();
        abort_if($works->isEmpty(), 404);

        return view('gallery3d', [
            'page' => [
                'title' => 'Satıştaki Tüm Eserler',
                'backUrl' => route('artworks'),
                'backLabel' => 'Eserlere dön',
            ],
            'payload' => [
                'mode' => 'hall',
                'main' => $this->present($works->first(), 'card'),
                'others' => $works->slice(1)->map(fn ($a) => $this->present($a, 'card'))->values(),
                'artist' => '',
                'subtitle' => 'Satıştaki Tüm Eserler',
                'artistPhoto' => null,
            ],
        ]);
    }

    private function artistWorks(int $artistId)
    {
        return Artwork::with('artist')
            ->active()
            ->where('artist_id', $artistId)
            ->whereNotNull('images')
            ->where('images', '!=', '[]')
            ->orderByDesc('is_featured')
            ->orderBy('is_sold')
            ->latest();
    }

    private function render(Artwork $main, $others, array $page)
    {
        $artist = $main->artist;

        return view('gallery3d', [
            'page' => $page,
            'payload' => [
                'main' => $this->present($main),
                'others' => $others->filter(fn ($a) => $a->first_image)->map(fn ($a) => $this->present($a))->values(),
                'artist' => $artist->name ?? '',
                'artistPhoto' => $artist && ($artist->avatar || $artist->image)
                    ? route('artwork.3d.artist', $artist)
                    : null,
            ],
        ]);
    }

    /**
     * WebGL texture'ı aynı origin'den gelmeli (Thumbor/R2 CORS başlığı göndermiyor).
     * Varsayılan: Thumbor'dan hızlı yüklenen boyut. ?full=1: R2'deki orijinal dosya.
     */
    public function image(Request $request, Artwork $artwork)
    {
        $path = $artwork->first_image;
        abort_unless($artwork->is_active && $path, 404);

        $headers = ['Cache-Control' => 'public, max-age=86400'];

        if ($request->boolean('full') && !str_starts_with($path, 'http')) {
            $disk = Storage::disk(config('filesystems.uploads'));
            abort_unless($disk->exists($path), 404);

            return $disk->response($path, null, $headers);
        }

        $size = in_array($request->query('size'), ['thumb', 'card', 'detail'], true) ? $request->query('size') : 'detail';
        $response = Http::timeout(20)->get(ImageUrl::make($path, $size));
        abort_unless($response->successful(), 404);

        return response($response->body(), 200, $headers + [
            'Content-Type' => $response->header('Content-Type') ?: 'image/jpeg',
        ]);
    }

    /**
     * Giriş duvarındaki sanatçı fotoğrafı (kare kırpılmış).
     */
    public function artistPhoto(Artist $artist)
    {
        $path = $artist->avatar ?: $artist->image;
        abort_unless($path, 404);

        $response = Http::timeout(20)->get(ImageUrl::make($path, 800, 800));
        abort_unless($response->successful(), 404);

        return response($response->body(), 200, [
            'Content-Type' => $response->header('Content-Type') ?: 'image/jpeg',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function present(Artwork $artwork, string $size = 'detail'): array
    {
        return [
            'id' => $artwork->id,
            'title' => $artwork->title,
            'artist' => $artwork->artist->name ?? '',
            'technique' => $artwork->technique,
            'year' => $artwork->year,
            'dimensionsText' => $artwork->dimensions,
            'dimensions' => ArtworkDimensions::parse($artwork->dimensions),
            'price' => $artwork->price_tl ? $artwork->formatted_price_tl : null,
            'sold' => (bool) $artwork->is_sold,
            'reserved' => (bool) $artwork->is_reserved,
            'image' => route('artwork.3d.image', $size === 'detail' ? [$artwork] : [$artwork, 'size' => $size]),
            'imageFull' => route('artwork.3d.image', [$artwork, 'full' => 1]),
            'url' => route('artwork.detail', $artwork->slug),
        ];
    }
}
