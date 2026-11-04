<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل التهجير: جدول عناصر الطلب مع حفظ نسخة من السعر والاسم وقت الشراء
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name'); // اسم الصنف المحفوظ في تاريخ الطلب
            $table->decimal('price', 8, 2); // سعر الوحدة الأساسي المحفوظ في تاريخ الطلب
            $table->unsignedInteger('quantity')->default(1);
            $table->json('addons_json')->nullable(); // الإضافات المختارة مع أسمائها وأسعارها
            $table->timestamps();

            $table->index(['order_id']);
        });
    }

    /**
     * التراجع عن التهجير
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

