<?php

namespace App\Livewire;

use App\Models\CartItem;
use Livewire\Component;

class Cart extends Component
{
    public function removeItem($id)
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        CartItem::where('id', $id)
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->delete();

        $this->dispatch('cart-updated');
        session()->flash('success', 'Eser sepetten kaldırıldı.');
    }

    public function render()
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        $cartItems = CartItem::with('artwork.artist')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->get();

        $totalTl = $cartItems->sum(fn($item) => $item->artwork->price_tl);
        $totalUsd = $cartItems->sum(fn($item) => $item->artwork->price_usd);

        return view('livewire.cart', [
            'cartItems' => $cartItems,
            'totalTl' => $totalTl,
            'totalUsd' => $totalUsd,
        ]);
    }
}
