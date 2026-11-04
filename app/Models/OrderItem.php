<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعيين
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'name',
        'price',
        'quantity',
        'addons_json',
    ];

    /**
     * تحويلات الأنواع
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'addons_json' => 'array',
        ];
    }

    /**
     * الطلب التابع له هذا الصنف
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * الصنف الأصلي في المنيو (قد يكون null إذا حُذف لاحقاً)
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * قائمة الإضافات المحفوظة مع الصنف
     *
     * @return array<int, array{id: int, name: string, price: float}>
     */
    public function getAddonsListAttribute(): array
    {
        return is_array($this->addons_json) ? $this->addons_json : [];
    }

    /**
     * إجمالي سعر الإضافات للقطعة الواحدة
     */
    public function getAddonsTotalAttribute(): float
    {
        $sum = 0.0;
        foreach ($this->addons_list as $addon) {
            $sum += (float) ($addon['price'] ?? 0);
        }
        return $sum;
    }

    /**
     * إجمالي السطر (سعر الصنف + إضافاته) مضروباً بالكمية
     */
    public function getLineTotalAttribute(): float
    {
        return ($this->price + $this->addons_total) * $this->quantity;
    }

    /**
     * سعر الصنف منسقاً
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ر.س';
    }

    /**
     * إجمالي السطر منسقاً
     */
    public function getFormattedLineTotalAttribute(): string
    {
        return number_format($this->line_total, 2) . ' ر.س';
    }
}

