<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل التهجير: إضافة حقول الكوبون ومبلغ الخصم لجدول الطلبات
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('address_id')->constrained('coupons')->nullOnDelete();
            $table->decimal('discount_amount', 8, 2)->default(0)->after('delivery_fee');
        });
    }

    /**
     * التراجع عن التهجير
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn('discount_amount');
        });
    }
};

