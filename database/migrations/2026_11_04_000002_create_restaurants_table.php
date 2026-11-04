<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل ترحيل إنشاء جدول المطاعم
     */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cuisine')->index(); // نوع المطبخ (شعبي، مشويات، بخاري، إلخ)
            $table->string('district')->index(); // الحي في المدينة
            $table->string('logo')->nullable(); // مسار شعار المطعم
            $table->string('cover')->nullable(); // مسار صورة الغلاف
            $table->decimal('min_order', 8, 2)->default(0); // الحد الأدنى للطلب بالريال
            $table->decimal('delivery_fee', 8, 2)->default(0); // رسوم التوصيل بالريال
            $table->unsignedInteger('prep_time')->default(30); // متوسط وقت التجهيز بالدقائق
            $table->boolean('is_open')->default(true)->index(); // هل يستقبل طلبات الآن؟
            $table->boolean('is_active')->default(true)->index(); // هل المطعم مفعل من الإدارة؟
            $table->timestamps();
        });
    }

    /**
     * التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};

