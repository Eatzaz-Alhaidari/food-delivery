<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    public const SESSION_KEY = 'sufra_cart';

    /**
     * استرجاع بنية السلة الخام من الجلسة (Session)
     *
     * @return array{restaurant_id: ?int, items: array<string, array{item_id: int, quantity: int, addon_ids: array<int>, notes: ?string}>}
     */
    public function getRawCart(): array
    {
        return Session::get(self::SESSION_KEY, [
            'restaurant_id' => null,
            'items' => [],
        ]);
    }

    /**
     * حفظ بنية السلة في الجلسة
     */
    protected function saveRawCart(array $cart): void
    {
        if (empty($cart['items'])) {
            Session::forget(self::SESSION_KEY);
        } else {
            Session::put(self::SESSION_KEY, $cart);
        }
    }

    /**
     * معرف المطعم الحالي المرتبطة به السلة
     */
    public function getRestaurantId(): ?int
    {
        return $this->getRawCart()['restaurant_id'] ?? null;
    }

    /**
     * استرجاع كائن المطعم الحالي طازجاً من قاعدة البيانات
     */
    public function getRestaurant(): ?Restaurant
    {
        $restaurantId = $this->getRestaurantId();
        if (! $restaurantId) {
            return null;
        }

        return Restaurant::find($restaurantId);
    }

    /**
     * هل السلة فارغة تماماً؟
     */
    public function isEmpty(): bool
    {
        $cart = $this->getRawCart();
        return empty($cart['items']);
    }

    /**
     * هل يمكن إضافة صنف من هذا المطعم دون تعارض مع مطعم آخر؟
     */
    public function canAddFromRestaurant(int $restaurantId): bool
    {
        $currentRestaurantId = $this->getRestaurantId();
        return $this->isEmpty() || $currentRestaurantId === null || $currentRestaurantId === $restaurantId;
    }

    /**
     * توليد مفتاح مميز لسطر السلة بناءً على الصنف والإضافات المختارة والملاحظة
     */
    public function generateItemKey(int $itemId, array $addonIds = [], ?string $notes = null): string
    {
        sort($addonIds);
        $cleanNotes = trim($notes ?? '');
        return md5($itemId . '_' . implode(',', $addonIds) . '_' . $cleanNotes);
    }

    /**
     * إضافة صنف مع كميته وإضافاته وملاحظته
     */
    public function add(MenuItem $item, int $quantity = 1, array $addonIds = [], ?string $notes = null): void
    {
        if ($quantity <= 0) {
            return;
        }

        $cart = $this->getRawCart();
        $itemRestaurantId = $item->category->restaurant_id;

        // إذا كانت السلة لمطعم آخر، يجب تفريغها أولاً عبر الواجهة، ولكن للاحتياط:
        if (! $this->canAddFromRestaurant($itemRestaurantId)) {
            $cart = [
                'restaurant_id' => $itemRestaurantId,
                'items' => [],
            ];
        } else {
            $cart['restaurant_id'] = $itemRestaurantId;
        }

        $key = $this->generateItemKey($item->id, $addonIds, $notes);

        if (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity'] += $quantity;
        } else {
            $cart['items'][$key] = [
                'item_id' => $item->id,
                'quantity' => $quantity,
                'addon_ids' => array_values(array_unique($addonIds)),
                'notes' => $notes ? trim($notes) : null,
            ];
        }

        $this->saveRawCart($cart);
    }

    /**
     * تعديل كمية سطر محدد في السلة
     */
    public function updateQuantity(string $itemKey, int $quantity): void
    {
        $cart = $this->getRawCart();

        if (! isset($cart['items'][$itemKey])) {
            return;
        }

        if ($quantity <= 0) {
            unset($cart['items'][$itemKey]);
        } else {
            $cart['items'][$itemKey]['quantity'] = min($quantity, 99);
        }

        if (empty($cart['items'])) {
            $cart['restaurant_id'] = null;
        }

        $this->saveRawCart($cart);
    }

    /**
     * حذف سطر من السلة
     */
    public function remove(string $itemKey): void
    {
        $cart = $this->getRawCart();

        if (isset($cart['items'][$itemKey])) {
            unset($cart['items'][$itemKey]);
        }

        if (empty($cart['items'])) {
            $cart['restaurant_id'] = null;
        }

        $this->saveRawCart($cart);
    }

    /**
     * تفريغ السلة بالكامل
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * عدد القطع الكلي داخل السلة
     */
    public function count(): int
    {
        $cart = $this->getRawCart();
        $count = 0;
        foreach ($cart['items'] as $item) {
            $count += (int) ($item['quantity'] ?? 0);
        }
        return $count;
    }

    /**
     * جلب عناصر السلة مهيأة بالأسعار والبيانات الحية من قاعدة البيانات
     * التزاماً بالمتطلب 5: إعادة احتساب الأسعار من DB وليس من بيانات المتصفح
     *
     * @return Collection<int, object>
     */
    public function getItems(): Collection
    {
        $cart = $this->getRawCart();
        if (empty($cart['items'])) {
            return collect();
        }

        $itemIds = array_column($cart['items'], 'item_id');
        $menuItems = MenuItem::with(['addons', 'category.restaurant'])
            ->whereIn('id', $itemIds)
            ->get()
            ->keyBy('id');

        $result = collect();

        foreach ($cart['items'] as $key => $raw) {
            $menuItem = $menuItems->get($raw['item_id']);
            if (! $menuItem) {
                continue;
            }

            // تصفية الإضافات والتأكد من أنها تتبع هذا الصنف فعلاً لحماية التسعير
            $chosenAddonIds = $raw['addon_ids'] ?? [];
            $validAddons = $menuItem->addons->whereIn('id', $chosenAddonIds);

            $addonsUnitPrice = (float) $validAddons->sum('extra_price');
            $unitBasePrice = (float) $menuItem->price;
            $unitTotalPrice = $unitBasePrice + $addonsUnitPrice;
            $quantity = (int) $raw['quantity'];
            $lineTotal = $unitTotalPrice * $quantity;

            $result->push((object) [
                'key' => $key,
                'menu_item' => $menuItem,
                'quantity' => $quantity,
                'unit_base_price' => $unitBasePrice,
                'addons' => $validAddons,
                'addons_unit_price' => $addonsUnitPrice,
                'unit_total_price' => $unitTotalPrice,
                'line_total' => $lineTotal,
                'notes' => $raw['notes'] ?? null,
                'is_available' => (bool) $menuItem->is_available,
                'formatted_unit_price' => number_format($unitTotalPrice, 2) . ' ر.س',
                'formatted_line_total' => number_format($lineTotal, 2) . ' ر.س',
            ]);
        }

        return $result;
    }

    /**
     * المجموع الفرعي المحتسب من أسعار قاعدة البيانات
     */
    public function subtotal(): float
    {
        return (float) $this->getItems()->sum('line_total');
    }

    /**
     * رسوم التوصيل من بيانات المطعم في DB
     */
    public function deliveryFee(): float
    {
        if ($this->isEmpty()) {
            return 0.0;
        }

        $restaurant = $this->getRestaurant();
        return $restaurant ? (float) $restaurant->delivery_fee : 0.0;
    }

    /**
     * الإجمالي النهائي (المجموع الفرعي + رسوم التوصيل)
     */
    public function total(): float
    {
        if ($this->isEmpty()) {
            return 0.0;
        }

        return $this->subtotal() + $this->deliveryFee();
    }

    /**
     * هل الطلب حقق الحد الأدنى لمطعم السلة؟
     */
    public function meetsMinOrder(): bool
    {
        $restaurant = $this->getRestaurant();
        if (! $restaurant) {
            return false;
        }

        return $this->subtotal() >= (float) $restaurant->min_order;
    }

    /**
     * المبلغ المتبقي لتحقيق الحد الأدنى
     */
    public function minOrderShortage(): float
    {
        $restaurant = $this->getRestaurant();
        if (! $restaurant) {
            return 0.0;
        }

        $min = (float) $restaurant->min_order;
        $sub = $this->subtotal();

        return $sub < $min ? ($min - $sub) : 0.0;
    }

    /**
     * التحقق الشامل من صلاحية السلة قبل إتمام الطلب
     *
     * @return array<string> قائمة بأي أخطاء تمنع إتمام الطلب
     */
    public function validateForCheckout(): array
    {
        $errors = [];

        if ($this->isEmpty()) {
            $errors[] = 'السلة فارغة. يرجى إضافة أصناف أولاً.';
            return $errors;
        }

        $restaurant = $this->getRestaurant();
        if (! $restaurant || ! $restaurant->is_active) {
            $errors[] = 'المطعم غير متاح للخدمة حالياً.';
            return $errors;
        }

        if (! $restaurant->is_open) {
            $errors[] = 'عذراً، المطعم مغلق حالياً ولا يستقبل طلبات جديدة.';
        }

        $items = $this->getItems();
        foreach ($items as $item) {
            if (! $item->is_available) {
                $errors[] = "الصنف \"{$item->menu_item->name}\" لم يعد متوفراً حالياً.";
            }
        }

        if (! $this->meetsMinOrder()) {
            $shortage = number_format($this->minOrderShortage(), 2);
            $minOrder = number_format($restaurant->min_order, 2);
            $errors[] = "لم تصل للحد الأدنى للطلب ({$minOrder} ر.س). تحتاج لإضافة ما قيمته {$shortage} ر.س لإتمام الطلب.";
        }

        return $errors;
    }
}

