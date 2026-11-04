<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_protected_customer_routes(): void
    {
        $this->get('/orders')->assertRedirect('/login');
        $this->get('/addresses')->assertRedirect('/login');
        $this->get('/checkout')->assertRedirect('/login');
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $this->actingAs($customer)
            ->get('/admin')
            ->assertStatus(403);
    }

    public function test_customer_cannot_access_restaurant_dashboard(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $this->actingAs($customer)
            ->get('/restaurant')
            ->assertStatus(403);
    }

    public function test_restaurant_owner_can_access_restaurant_dashboard(): void
    {
        $owner = User::factory()->create(['role' => UserRole::Restaurant]);
        Restaurant::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->get('/restaurant')
            ->assertStatus(200);

        $this->actingAs($owner)
            ->get('/restaurant/orders')
            ->assertStatus(200);
    }

    public function test_restaurant_owner_cannot_access_admin_dashboard(): void
    {
        $owner = User::factory()->create(['role' => UserRole::Restaurant]);
        Restaurant::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->get('/admin')
            ->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard_and_subpages(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/admin/restaurants')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/admin/orders')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/admin/coupons')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/admin/sales')
            ->assertStatus(200);
    }
}

