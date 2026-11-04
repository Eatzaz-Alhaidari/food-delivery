<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل ترحيل إنشاء جدول أصناف قائمة الطعام
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // اسم الصنف
            $table->text('description')->nullable(); // وصف الصنف والمكونات
            $table->decimal('price', 8, 2); // سعر الصنف بالريال
            $table->string('image')->nullable(); // مسار صورة الصنف
            $table->boolean('is_available')->default(true)->index(); // هل الصنف متوفر أم نفد؟
            $table->timestamps();
        });
    }

    /**
     * التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};

