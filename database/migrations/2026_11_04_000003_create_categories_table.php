<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل ترحيل إنشاء جدول تصنيفات قائمة الطعام
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // اسم التصنيف (أطباق رئيسية، مقبلات، مشروبات...)
            $table->integer('sort_order')->default(0)->index(); // ترتيب العرض
            $table->timestamps();
        });
    }

    /**
     * التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

