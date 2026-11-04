<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل ترحيل إنشاء جدول إضافات الأصناف
     */
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // اسم الإضافة (مثال: طحينة إضافية، زيادة رز...)
            $table->decimal('extra_price', 8, 2)->default(0); // التكلفة الإضافية بالريال
            $table->timestamps();
        });
    }

    /**
     * التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};

