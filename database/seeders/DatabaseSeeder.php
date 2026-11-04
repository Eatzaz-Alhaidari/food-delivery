<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * تشغيل البذور الأساسية للتطبيق
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RestaurantSeeder::class,
            OrderSeeder::class,
            CouponSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
