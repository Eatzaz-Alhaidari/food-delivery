<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $restaurantOwner;
    protected Restaurant $restaurant;
    protected Address $address;
    protected MenuItem $menuItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => UserRole::Customer]);
        $this->restaurantOwner = User::factory()->create(['role' => UserRole::Restaurant]);
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => $this->restaurantOwner->id,
            'delivery_fee' => 15.00,
            'min_order' => 40.00,
            'is_open' => true,
            'is_active' => true,
        ]);

        $category = Category::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'الأطباق الرئيسية',
            'sort_order' => 1,
        ]);

        $this->menuItem = MenuItem::create([
            'category_id' => $category->id,
            'name' => 'كبسة لحم نعيمي',
            'description' => 'كبسة سعودية طازجة مع الأرز البشاور والمكسرات',
            'price' => 65.00,
            'is_available' => true,
        ]);

        $this->address = Address::create([
            'user_id' => $this->customer->id,
            'title' => 'المنزل',
            'district' => 'الياسمين',
            'street' => 'شارع أنس بن مالك',
        ]);
    }

    public function test_order_creation_stores_order_items_and_financials(): void
    {
        $quantity = 2;
        $subtotal = 65.00 * $quantity; // 130.00
        $deliveryFee = 15.00;
        $total = $subtotal + $deliveryFee; // 145.00

        $order = Order::create([
            'user_id' => $this->customer->id,
            'restaurant_id' => $this->restaurant->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::Pending,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'discount_amount' => 0.00,
            'total' => $total,
            'payment_method' => 'cash',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $this->menuItem->id,
            'name' => $this->menuItem->name,
            'price' => $this->menuItem->price,
            'quantity' => $quantity,
            'addons_json' => null,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->customer->id,
            'total' => 145.00,
        ]);

        $this->assertCount(1, $order->items);
        $this->assertEquals(130.00, (float) $order->items->first()->price * $order->items->first()->quantity);
    }

    public function test_coupon_discount_applied_correctly_and_increments_used_count(): void
    {
        $coupon = Coupon::create([
            'code' => 'SUFRA10',
            'type' => 'percent',
            'value' => 10.00,
            'min_order' => 50.00,
            'expires_at' => now()->addMonth(),
            'max_uses' => 100,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $subtotal = 100.00;
        $discount = $coupon->calculateDiscount($subtotal); // 10.00
        $deliveryFee = 15.00;
        $total = max(0, $subtotal - $discount) + $deliveryFee; // 105.00

        $order = Order::create([
            'user_id' => $this->customer->id,
            'restaurant_id' => $this->restaurant->id,
            'address_id' => $this->address->id,
            'coupon_id' => $coupon->id,
            'status' => OrderStatus::Pending,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'discount_amount' => $discount,
            'total' => $total,
            'payment_method' => 'card_mock',
        ]);

        $coupon->increment('used_count');

        $this->assertEquals(10.00, (float) $order->discount_amount);
        $this->assertEquals(105.00, (float) $order->total);
        $this->assertEquals(1, $coupon->fresh()->used_count);
    }

    public function test_customer_can_review_only_delivered_order_once(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'restaurant_id' => $this->restaurant->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::Delivered,
            'subtotal' => 65.00,
            'delivery_fee' => 15.00,
            'discount_amount' => 0.00,
            'total' => 80.00,
            'payment_method' => 'cash',
        ]);

        $this->assertTrue($order->canBeReviewedByCustomer());

        Review::create([
            'order_id' => $order->id,
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->customer->id,
            'rating' => 5,
            'comment' => 'طعام رائع وتوصيل سريع جداً.',
        ]);

        // بعد إضافة التقييم لا يمكن التقييم مرة أخرى
        $this->assertFalse($order->fresh()->canBeReviewedByCustomer());
    }
}

