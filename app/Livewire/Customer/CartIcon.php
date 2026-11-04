<?php

namespace App\Livewire\Customer;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    public int $count = 0;

    public function mount(CartService $cart): void
    {
        $this->count = $cart->count();
    }

    #[On('cart-updated')]
    public function updateCount(CartService $cart): void
    {
        $this->count = $cart->count();
    }

    public function openDrawer(): void
    {
        $this->dispatch('open-cart-drawer');
    }

    public function render()
    {
        return view('livewire.customer.cart-icon');
    }
}

