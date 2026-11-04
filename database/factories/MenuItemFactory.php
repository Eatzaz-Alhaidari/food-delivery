<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->randomElement(['كبسة حاشي', 'مندي لحم', 'جريش أحمر', 'مطبق مالح', 'شاورما لحم']),
            'description' => fake()->sentence(8),
            'price' => fake()->randomElement([22, 28, 35, 45, 65, 80]),
            'image' => null,
            'is_available' => true,
        ];
    }
}

