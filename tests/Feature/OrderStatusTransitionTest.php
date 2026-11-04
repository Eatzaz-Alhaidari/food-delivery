<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $restaurantOwner;
    protected Restaurant $restaurant;
    protected Address $address;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => UserRole::Customer]);
        $this->restaurantOwner = User::factory()->create(['role' => UserRole::Restaurant]);
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->restaurantOwner->id]);
        $this->address = Address::create([
            'user_id' => $this->customer->id,
            'title' => 'المنزل',
            'district' => 'حطين',
            'street' => 'طريق تركي الأول',
        ]);
    }

    public function test_order_can_transition_sequentially_through_all_happy_path_states(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'restaurant_id' => $this->restaurant->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::Pending,
            'subtotal' => 80.00,
            'delivery_fee' => 12.00,
            'discount_amount' => 0.00,
            'total' => 92.00,
            'payment_method' => 'cash',
        ]);

        // 1. Pending -> Accepted
        $order->transitionTo(OrderStatus::Accepted, $this->restaurantOwner, 'تم قبول الطلب');
        $this->assertEquals(OrderStatus::Accepted, $order->fresh()->status);

        // 2. Accepted -> Preparing
        $order->transitionTo(OrderStatus::Preparing, $this->restaurantOwner, 'جاري تحضير الوجبة');
        $this->assertEquals(OrderStatus::Preparing, $order->fresh()->status);

        // 3. Preparing -> Ready
        $order->transitionTo(OrderStatus::Ready, $this->restaurantOwner, 'الوجبة جاهزة بانتظار السائق');
        $this->assertEquals(OrderStatus::Ready, $order->fresh()->status);

        // 4. Ready -> OutForDelivery
        $order->transitionTo(OrderStatus::OutForDelivery, $this->restaurantOwner, 'خرج الطلب مع السائق');
        $this->assertEquals(OrderStatus::OutForDelivery, $order->fresh()->status);

        // 5. OutForDelivery -> Delivered
        $order->transitionTo(OrderStatus::Delivered, $this->restaurantOwner, 'تم التسليم للعميل');
        $this->assertEquals(OrderStatus::Delivered, $order->fresh()->status);

        // التأكد من تسجيل كافة السجلات في جدول order_status_logs
        $this->assertCount(5, $order->statusLogs);
    }

    public function test_prevent_jumping_from_pending_directly_to_delivered(): void
    {
        $this->expectException(DomainException::class);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'restaurant_id' => $this->restaurant->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::Pending,
            'subtotal' => 50.00,
            'delivery_fee' => 10.00,
            'discount_amount' => 0.00,
            'total' => 60.00,
            'payment_method' => 'cash',
        ]);

        // محاولة القفز الممنوعة
        $order->transitionTo(OrderStatus::Delivered, $this->restaurantOwner);
    }

    public function test_prevent_transitioning_backwards(): void
    {
        $this->expectException(DomainException::class);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'restaurant_id' => $this->restaurant->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::Delivered,
            'subtotal' => 50.00,
            'delivery_fee' => 10.00,
            'discount_amount' => 0.00,
            'total' => 60.00,
            'payment_method' => 'cash',
        ]);

        // محاولة الرجوع من Delivered إلى Preparing
        $order->transitionTo(OrderStatus::Preparing, $this->restaurantOwner);
    }

    public function test_order_can_be_rejected_from_pending_with_required_reason(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'restaurant_id' => $this->restaurant->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::Pending,
            'subtotal' => 50.00,
            'delivery_fee' => 10.00,
            'discount_amount' => 0.00,
            'total' => 60.00,
            'payment_method' => 'cash',
        ]);

        $order->transitionTo(OrderStatus::Rejected, $this->restaurantOwner, 'نعتذر، انتهت الكمية المتوفرة من هذا الصنف.');
        $this->assertEquals(OrderStatus::Rejected, $order->fresh()->status);
        $this->assertEquals('نعتذر، انتهت الكمية المتوفرة من هذا الصنف.', $order->statusLogs()->latest()->first()->comment);
    }
}

