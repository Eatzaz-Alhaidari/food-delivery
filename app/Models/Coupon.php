<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعيين
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order',
        'expires_at',
        'max_uses',
        'used_count',
        'is_active',
    ];

    /**
     * تحويلات الأنواع
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order' => 'decimal:2',
            'expires_at' => 'datetime',
            'max_uses' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * الطلبات التي تم تطبيق هذا الكوبون عليها
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * التحقق من صلاحية الكوبون لمجموع فرعي محدد وإرجاع سبب الخطأ إن وُجد (المتطلب 5)
     *
     * @return array{valid: bool, message: ?string}
     */
    public function validateForSubtotal(float $subtotal): array
    {
        if (! $this->is_active) {
            return [
                'valid' => false,
                'message' => 'عذراً، هذا الكوبون غير مفعّل حالياً.',
            ];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return [
                'valid' => false,
                'message' => 'عذراً، انتهت صلاحية هذا الكوبون في ' . $this->expires_at->format('Y/m/d') . '.',
            ];
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return [
                'valid' => false,
                'message' => 'عذراً، تم استنفاد الحد الأقصى المتاح لاستخدام هذا الكوبون.',
            ];
        }

        if ($this->min_order !== null && $subtotal < (float) $this->min_order) {
            $formattedMin = number_format($this->min_order, 2);
            return [
                'valid' => false,
                'message' => "يتطلب هذا الكوبون حداً أدنى للطلب بقيمة {$formattedMin} ر.س.",
            ];
        }

        return [
            'valid' => true,
            'message' => null,
        ];
    }

    /**
     * احتساب مبلغ الخصم الدقيق مع ضمان عدم تجاوز المجموع الفرعي (المتطلب 5)
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        if ($this->type === 'percent') {
            $discount = round($subtotal * ((float) $this->value / 100), 2);
        } else {
            $discount = (float) $this->value;
        }

        // منع أن يصبح الإجمالي سالباً نهائياً
        return (float) min($discount, $subtotal);
    }

    /**
     * نص القيمة المنسق بالعربية
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percent') {
            return (float) $this->value . '%';
        }

        return number_format($this->value, 2) . ' ر.س';
    }

    /**
     * نوع الكوبون بالعربية
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'percent' ? 'نسبة مئوية' : 'مبلغ ثابت';
    }
}

