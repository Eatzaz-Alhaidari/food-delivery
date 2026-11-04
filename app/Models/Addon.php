<?php

namespace App\Models;

use Database\Factories\AddonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Addon extends Model
{
    /** @use HasFactory<AddonFactory> */
    use HasFactory;

    /**
     * الحقول القابلة للتعيين
     *
     * @var list<string>
     */
    protected $fillable = [
        'menu_item_id',
        'name',
        'extra_price',
    ];

    /**
     * تحويلات الأنواع
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'extra_price' => 'decimal:2',
        ];
    }

    /**
     * الصنف التابع له هذه الإضافة
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * سعر الإضافة منسقاً بالريال
     */
    public function getFormattedExtraPriceAttribute(): string
    {
        if ($this->extra_price <= 0) {
            return 'مجاناً';
        }

        return '+ ' . number_format($this->extra_price, 0) . ' ر.س';
    }
}

