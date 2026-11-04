<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل التهجير: جدول عناوين التوصيل للعملاء
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // مثلاً: المنزل، العمل، الاستراحة
            $table->string('district'); // الحي
            $table->string('street'); // الشارع
            $table->text('details')->nullable(); // تفاصيل إضافية: رقم الشقة، الدور، معالم قريبة
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * التراجع عن التهجير
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

