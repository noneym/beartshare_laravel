<?php

namespace App\Livewire;

use App\Models\Artwork;
use App\Models\CartItem;
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
            session()->flash('error', 'Bu eser satılmıştır.');
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
            session()->flash('info', 'Bu eser zaten sepetinizde.');
            return;
        }

        CartItem::create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
            'artwork_id' => $this->artwork->id,
        ]);

        $this->dispatch('cart-updated');
        session()->flash('success', 'Eser sepete eklendi.');
    }

    public function render()
    {
        $relatedArtworks = Artwork::with('artist')
            ->available()
            ->where('artist_id', $this->artwork->artist_id)
            ->where('id', '!=', $this->artwork->id)
            ->take(4)
            ->get();

        return view('livewire.artwork-detail', [
            'relatedArtworks' => $relatedArtworks,
        ]);
    }
}
