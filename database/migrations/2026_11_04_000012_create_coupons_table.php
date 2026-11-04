<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل التهجير: جدول كوبونات الخصم
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود فريد (مثل: SUFRA10, RAMADAN)
            $table->string('type')->default('percent'); // percent (نسبة مئوية) أو fixed (مبلغ ثابت)
            $table->decimal('value', 8, 2); // قيمة الخصم (مثلاً 10% أو 20 ر.س)
            $table->decimal('min_order', 8, 2)->nullable(); // حد أدنى للطلب لتفعيل الكوبون
            $table->dateTime('expires_at')->nullable(); // تاريخ انتهاء الصلاحية
            $table->unsignedInteger('max_uses')->nullable(); // أقصى عدد استخدامات كلي للكوبون
            $table->unsignedInteger('used_count')->default(0); // عدد مرات الاستخدام المحققة
            $table->boolean('is_active')->default(true); // تفعيل أو إيقاف الكوبون
            $table->timestamps();

            $table->index(['code', 'is_active']);
        });
    }

    /**
     * التراجع عن التهجير
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

