<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * بذر حسابات الأدوار الثلاثة في منصة سُفرة
     */
    public function run(): void
    {
        // ١. حساب مدير المنصة
        $admin = User::firstOrNew(['email' => 'admin@sufra.test']);
        $admin->forceFill([
            'name' => 'مدير المنصة',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ])->save();

        // ٢. حساب ممثل/صاحب المطعم
        $restaurant = User::firstOrNew(['email' => 'restaurant@sufra.test']);
        $restaurant->forceFill([
            'name' => 'مطعم شواية نجد',
            'password' => Hash::make('password'),
            'role' => UserRole::Restaurant,
            'email_verified_at' => now(),
        ])->save();

        // ٣. حساب العميل
        $customer = User::firstOrNew(['email' => 'customer@sufra.test']);
        $customer->forceFill([
            'name' => 'سارة العبدالله',
            'password' => Hash::make('password'),
            'role' => UserRole::Customer,
            'email_verified_at' => now(),
        ])->save();

        // عناوين تجريبية للعميل لاختبار التوصيل وإتمام الطلب
        $customer->addresses()->firstOrCreate(
            ['title' => 'المنزل'],
            [
                'district' => 'حطين',
                'street' => 'شارع الأمير تركي بن عبدالعزيز الأول',
                'details' => 'فيلا 14، بجوار حديقة حطين',
            ]
        );

        $customer->addresses()->firstOrCreate(
            ['title' => 'العمل'],
            [
                'district' => 'العليا',
                'street' => 'طريق الملك فهد',
                'details' => 'برج الفيصلية، الدور 12، مكتب 1204',
            ]
        );
    }
}

