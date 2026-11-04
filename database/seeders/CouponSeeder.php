<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * بذر كوبونات تجريبية لاختبار الكوبونات في السلة ولوحة المدير
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'SUFRA10',
                'type' => 'percent',
                'value' => 10.00,
                'min_order' => 30.00,
                'expires_at' => Carbon::now()->addMonths(3),
                'max_uses' => 500,
                'used_count' => 12,
                'is_active' => true,
            ],
            [
                'code' => 'KABSA20',
                'type' => 'fixed',
                'value' => 20.00,
                'min_order' => 60.00,
                'expires_at' => Carbon::now()->addMonths(1),
                'max_uses' => 200,
                'used_count' => 45,
                'is_active' => true,
            ],
            [
                'code' => 'WELCOME15',
                'type' => 'percent',
                'value' => 15.00,
                'min_order' => 40.00,
                'expires_at' => Carbon::now()->addMonths(6),
                'max_uses' => 1000,
                'used_count' => 88,
                'is_active' => true,
            ],
            [
                'code' => 'HALA10',
                'type' => 'fixed',
                'value' => 10.00,
                'min_order' => 25.00,
                'expires_at' => Carbon::now()->addMonths(2),
                'max_uses' => 300,
                'used_count' => 5,
                'is_active' => true,
            ],
            [
                'code' => 'EXPIRED',
                'type' => 'percent',
                'value' => 25.00,
                'min_order' => 50.00,
                'expires_at' => Carbon::now()->subDays(5),
                'max_uses' => 100,
                'used_count' => 100,
                'is_active' => true,
            ],
            [
                'code' => 'MAXED',
                'type' => 'fixed',
                'value' => 50.00,
                'min_order' => 100.00,
                'expires_at' => Carbon::now()->addMonth(),
                'max_uses' => 10,
                'used_count' => 10,
                'is_active' => true,
            ],
            [
                'code' => 'INACTIVE',
                'type' => 'percent',
                'value' => 30.00,
                'min_order' => null,
                'expires_at' => Carbon::now()->addMonths(2),
                'max_uses' => null,
                'used_count' => 0,
                'is_active' => false,
            ],
        ];

        foreach ($coupons as $data) {
            Coupon::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}

