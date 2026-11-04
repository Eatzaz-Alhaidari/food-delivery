<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => fake()->randomElement(['الأطباق الرئيسية', 'المقبلات والإيدامات', 'المشروبات والحلويات']),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}

