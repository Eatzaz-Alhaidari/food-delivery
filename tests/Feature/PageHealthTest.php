<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageHealthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_pages_render_successfully(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $response = $this->get(route('restaurants.index'));
        $response->assertStatus(200);

        $restaurant = Restaurant::first();
        $response = $this->get(route('restaurants.show', $restaurant));
        $response->assertStatus(200);

        $response = $this->get(route('login'));
        $response->assertStatus(200);

        $response = $this->get(route('register'));
        $response->assertStatus(200);

        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
    }

    public function test_customer_pages_render_successfully(): void
    {
        $customer = User::where('role', 'customer')->first();

        // إذا كانت السلة فارغة، يوجه تلقائياً إلى المطاعم
        $response = $this->actingAs($customer)->get(route('checkout'));
        $response->assertRedirect(route('restaurants.index'));

        // مع وجود عناصر في السلة تتجاوز الحد الأدنى للطلب
        $item = \App\Models\MenuItem::first();
        app(\App\Services\CartService::class)->add($item, 5);
        $response = $this->actingAs($customer)->get(route('checkout'));
        $response->assertStatus(200);

        $response = $this->actingAs($customer)->get(route('orders.index'));
        $response->assertStatus(200);

        $order = Order::where('customer_id', $customer->id)->first();
        if ($order) {
            $response = $this->actingAs($customer)->get(route('orders.show', $order));
            $response->assertStatus(200);
        }

        $response = $this->actingAs($customer)->get(route('addresses.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($customer)->get(route('profile.edit'));
        $response->assertStatus(200);
    }

    public function test_restaurant_dashboard_pages_render_successfully(): void
    {
        $restaurantOwner = User::where('role', 'restaurant')->first();

        $response = $this->actingAs($restaurantOwner)->get(route('restaurant.dashboard'));
        $response->assertStatus(200);

        $response = $this->actingAs($restaurantOwner)->get(route('restaurant.menu'));
        $response->assertStatus(200);

        $response = $this->actingAs($restaurantOwner)->get(route('restaurant.orders'));
        $response->assertStatus(200);
    }

    public function test_admin_dashboard_pages_render_successfully(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('admin.restaurants'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('admin.orders'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('admin.sales'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('admin.settings'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('admin.coupons'));
        $response->assertStatus(200);
    }
}
