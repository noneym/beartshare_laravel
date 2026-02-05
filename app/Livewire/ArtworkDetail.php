<?php

namespace App\Livewire;

use App\Models\Artwork;
use App\Models\CartItem;
use Illuminate\Support\Str;
use Livewire\Component;

class ArtworkDetail extends Component
{
    public Artwork $artwork;
    public $currentImage = 0;

    public function mount($slug)
    {
        $this->artwork = Artwork::with('artist', 'category')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function setImage($index)
    {
        $this->currentImage = $index;
    }

    public function addToCart()
    {
        if ($this->artwork->is_sold) {
            $this->dispatch('cart-error', message: 'Bu eser satılmıştır.');
            return;
        }

        $userId = auth()->id();
        $sessionId = session()->getId();

        $exists = CartItem::where('artwork_id', $this->artwork->id)
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->exists();

        if ($exists) {
            $this->dispatch('cart-info', message: 'Bu eser zaten sepetinizde.');
            return;
        }

        CartItem::create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
            'artwork_id' => $this->artwork->id,
        ]);

        $this->dispatch('cart-updated');
        $this->dispatch('cart-added', message: 'Eser sepete eklendi!');
    }

    public function render()
    {
        $relatedArtworks = Artwork::with('artist')
            ->available()
            ->where('artist_id', $this->artwork->artist_id)
            ->where('id', '!=', $this->artwork->id)
            ->take(4)
            ->get();

        $artwork = $this->artwork;
        $artistName = $artwork->artist ? $artwork->artist->name : 'Bilinmeyen Sanatçı';
        $description = $artwork->description
            ? Str::limit(strip_tags(html_entity_decode($artwork->description, ENT_QUOTES, 'UTF-8')), 160)
            : "{$artwork->title} - {$artistName} tarafından. BeArtShare online sanat galerisinde orijinal eserleri keşfedin.";

        $price = $artwork->price_tl ? number_format($artwork->price_tl, 0, ',', '.') . ' ₺' : '';
        $category = $artwork->category ? $artwork->category->name : '';
        $imageUrl = $artwork->image_url;

        $jsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $artwork->title,
            'description' => $description,
            'image' => $imageUrl,
            'brand' => [
                '@type' => 'Brand',
                'name' => $artistName,
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $artwork->price_tl ?? 0,
                'priceCurrency' => 'TRY',
                'availability' => $artwork->is_sold
                    ? 'https://schema.org/SoldOut'
                    : 'https://schema.org/InStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'BeArtShare',
                ],
            ],
            'category' => $category,
            'url' => url()->current(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return view('livewire.artwork-detail', [
            'relatedArtworks' => $relatedArtworks,
        ])->layoutData([
            'title' => "{$artwork->title} - {$artistName} | BeArtShare",
            'metaDescription' => $description,
            'metaKeywords' => implode(', ', array_filter([$artwork->title, $artistName, $category, 'orijinal eser', 'sanat eseri', 'tablo'])),
            'ogType' => 'product',
            'ogTitle' => "{$artwork->title} - {$artistName}",
            'ogDescription' => $description,
            'ogImage' => $imageUrl,
            'jsonLd' => $jsonLd,
        ]);
    }
}
