<?php

namespace App\Livewire\Customer;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $isOpen = false;

    #[On('open-cart-drawer')]
    public function open(): void
    {
        $this->isOpen = true;
    }

    #[On('close-cart-drawer')]
    public function close(): void
    {
        $this->isOpen = false;
        $this->dispatch('close-cart-drawer');
    }

    #[On('cart-updated')]
    public function onCartUpdated(): void
    {
        // إعادة الرسم التلقائي عند تحديث محتويات السلة
    }

    public function increment(string $key, CartService $cart): void
    {
        $raw = $cart->getRawCart()['items'][$key] ?? null;
        if ($raw) {
            $cart->updateQuantity($key, (int) $raw['quantity'] + 1);
            $this->dispatch('cart-updated');
        }
    }

    public function decrement(string $key, CartService $cart): void
    {
        $raw = $cart->getRawCart()['items'][$key] ?? null;
        if ($raw) {
            $cart->updateQuantity($key, (int) $raw['quantity'] - 1);
            $this->dispatch('cart-updated');
        }
    }

    public function removeItem(string $key, CartService $cart): void
    {
        $cart->remove($key);
        $this->dispatch('cart-updated');
    }

    public function clearCart(CartService $cart): void
    {
        $cart->clear();
        $this->dispatch('cart-updated');
    }

    public function render(CartService $cart)
    {
        return view('livewire.customer.cart-drawer', [
            'restaurant' => $cart->getRestaurant(),
            'items' => $cart->getItems(),
            'subtotal' => $cart->subtotal(),
            'deliveryFee' => $cart->deliveryFee(),
            'total' => $cart->total(),
            'meetsMinOrder' => $cart->meetsMinOrder(),
            'minOrderShortage' => $cart->minOrderShortage(),
            'isEmpty' => $cart->isEmpty(),
        ]);
    }
}

