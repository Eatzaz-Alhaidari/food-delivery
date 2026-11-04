<?php

namespace Database\Factories;

use App\Models\Addon;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Addon>
 */
class AddonFactory extends Factory
{
    protected $model = Addon::class;

    public function definition(): array
    {
        return [
            'menu_item_id' => MenuItem::factory(),
            'name' => fake()->randomElement(['رز إضافي', 'طحينة', 'سلطة خضراء', 'صوص حار', 'جبن زيادة']),
            'extra_price' => fake()->randomElement([3, 5, 8, 10]),
        ];
    }
}

