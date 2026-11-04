<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusLog extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعيين
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'from_status',
        'to_status',
        'changed_by_user_id',
        'comment',
    ];

    /**
     * تحويلات الأنواع
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'from_status' => OrderStatus::class,
            'to_status' => OrderStatus::class,
        ];
    }

    /**
     * الطلب المرتبط بهذا السجل
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * المستخدم الذي قام بتغيير الحالة
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    /**
     * اسم الشخص الذي غيّر الحالة باللغة العربية
     */
    public function getChangedByNameAttribute(): string
    {
        if (! $this->changedBy) {
            return 'النظام الآلي';
        }

        if ($this->changedBy->isRestaurant()) {
            return 'إدارة المطعم';
        }

        if ($this->changedBy->isAdmin()) {
            return 'مدير المنصة';
        }

        return 'العميل (' . $this->changedBy->name . ')';
    }
}

