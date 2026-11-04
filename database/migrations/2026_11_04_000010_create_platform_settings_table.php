<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل التهجير: جدول إعدادات المنصة (عمولة المنصة والسياسات)
     */
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, float, boolean
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // بذر الإعداد الافتراضي لعمولة المنصة بنسبة 10% بحسب المتطلب 6
        DB::table('platform_settings')->insert([
            'key' => 'commission_percentage',
            'value' => '10',
            'type' => 'float',
            'description' => 'نسبة عمولة المنصة الافتراضية من مبيعات المطاعم',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * التراجع عن التهجير
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};

