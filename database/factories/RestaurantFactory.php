<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Restaurant>
 */
class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        $name = fake()->company();
        return [
            'user_id' => User::factory()->state(['role' => UserRole::Restaurant]),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 999),
            'description' => fake()->realText(100),
            'cuisine' => fake()->randomElement(['شعبي', 'مشويات', 'بخاري', 'فطور شعبي', 'شاورما', 'حلى ومخبوزات', 'برجر']),
            'district' => fake()->randomElement(['الصحافة', 'الملقا', 'العليا', 'النرجس', 'الياسمين', 'الملز', 'السليمانية', 'الروضة']),
            'logo' => null,
            'cover' => null,
            'min_order' => fake()->randomElement([25, 30, 40, 50]),
            'delivery_fee' => fake()->randomElement([0, 9, 12, 15]),
            'prep_time' => fake()->randomElement([20, 25, 30, 40]),
            'is_open' => true,
            'is_active' => true,
        ];
    }
}

