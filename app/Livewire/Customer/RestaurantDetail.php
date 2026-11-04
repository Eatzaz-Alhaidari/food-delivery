<?php

namespace App\Livewire\Customer;

use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Services\CartService;
use Livewire\Component;

class RestaurantDetail extends Component
{
    public Restaurant $restaurant;

    // حالة نافذة تخصيص الصنف
    public bool $showItemModal = false;
    public ?int $selectedItemId = null;
    public ?MenuItem $selectedItem = null;
    public int $quantity = 1;
    public array $selectedAddons = [];
    public ?string $notes = '';

    // حالة نافذة تأكيد تفريغ السلة عند التعارض مع مطعم آخر
    public bool $showClearCartConfirmModal = false;
    public ?int $pendingItemId = null;
    public ?string $previousRestaurantName = null;

    /**
     * تحميل بيانات المطعم مع فحص التفعيل وتطبيق الـ Eager Loading
     */
    public function mount(string $slug): void
    {
        // ١. استعلام المطعم بالـ slug والتأكد من وجوده
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        // ٢. المطعم غير النشط يعطي 404 صراحة (المتطلب 4)
        if (! $restaurant->is_active) {
            abort(404, 'المطعم غير متاح حالياً.');
        }

        // ٣. Eager Loading لمنع استعلامات N+1 (المتطلب 5)
        $restaurant->load([
            'categories' => function ($q) {
                $q->orderBy('sort_order');
            },
            'categories.menuItems' => function ($q) {
                $q->with('addons');
            },
            'reviews.user',
        ]);

        $this->restaurant = $restaurant;
    }

    /**
     * فتح نافذة الصنف أو نافذة التعارض إن كانت السلة لمطعم آخر
     */
    public function openItemModal(int $itemId, CartService $cart): void
    {
        if (! $this->restaurant->is_open) {
            return;
        }

        // التحقق من تعارض المطاعم في السلة (المتطلب 2)
        if (! $cart->canAddFromRestaurant($this->restaurant->id)) {
            $this->pendingItemId = $itemId;
            $this->previousRestaurantName = $cart->getRestaurant()?->name ?? 'مطعم آخر';
            $this->showClearCartConfirmModal = true;
            return;
        }

        $this->prepareItemModal($itemId);
    }

    /**
     * تأكيد تفريغ السلة والمتابعة بإضافة الصنف من المطعم الجديد
     */
    public function confirmClearCartAndContinue(CartService $cart): void
    {
        $cart->clear();
        $this->dispatch('cart-updated');
        $this->showClearCartConfirmModal = false;

        if ($this->pendingItemId) {
            $this->prepareItemModal($this->pendingItemId);
            $this->pendingItemId = null;
        }
    }

    /**
     * إلغاء تفريغ السلة وإغلاق نافذة التنبيه
     */
    public function cancelClearCart(): void
    {
        $this->showClearCartConfirmModal = false;
        $this->pendingItemId = null;
    }

    /**
     * تجهيز بيانات الصنف وإظهار نافذة الاختيار (المتطلب 4)
     */
    protected function prepareItemModal(int $itemId): void
    {
        $item = MenuItem::with('addons')->find($itemId);

        if (! $item || ! $item->is_available) {
            return;
        }

        $this->selectedItem = $item;
        $this->selectedItemId = $item->id;
        $this->quantity = 1;
        $this->selectedAddons = [];
        $this->notes = '';
        $this->showItemModal = true;
    }

    /**
     * إغلاق نافذة تخصيص الصنف وتصفير البيانات
     */
    public function closeItemModal(): void
    {
        $this->showItemModal = false;
        $this->selectedItem = null;
        $this->selectedItemId = null;
        $this->quantity = 1;
        $this->selectedAddons = [];
        $this->notes = '';
    }

    /**
     * زيادة الكمية
     */
    public function incrementQuantity(): void
    {
        if ($this->quantity < 99) {
            $this->quantity++;
        }
    }

    /**
     * إنقاص الكمية
     */
    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    /**
     * حساب إجمالي الصنف المختار مع إضافاته ديناميكياً
     */
    public function getItemModalTotalProperty(): float
    {
        if (! $this->selectedItem) {
            return 0.0;
        }

        $base = (float) $this->selectedItem->price;
        $addonsSum = 0.0;

        if ($this->selectedItem->addons->isNotEmpty()) {
            $addonsSum = (float) $this->selectedItem->addons
                ->whereIn('id', $this->selectedAddons)
                ->sum('extra_price');
        }

        return ($base + $addonsSum) * $this->quantity;
    }

    /**
     * إضافة الصنف إلى السلة وتحديث الواجهة والدرج المنزلق
     */
    public function addToCart(CartService $cart): void
    {
        if (! $this->selectedItem || ! $this->restaurant->is_open || ! $this->selectedItem->is_available) {
            return;
        }

        $cart->add(
            item: $this->selectedItem,
            quantity: $this->quantity,
            addonIds: $this->selectedAddons,
            notes: $this->notes
        );

        $this->closeItemModal();
        $this->dispatch('cart-updated');
        $this->dispatch('open-cart-drawer');
    }

    public function render()
    {
        return view('livewire.customer.restaurant-detail')
            ->layout('layouts.app', ['title' => $this->restaurant->name]);
    }
}
