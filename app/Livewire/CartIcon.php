<?php

namespace App\Livewire;

use App\Models\CartItem;
use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    #[On('cart-updated')]
    public function updateCount()
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        $this->count = CartItem::when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->count();
    }

    public function render()
    {
        return view('livewire.cart-icon');
    }
}
