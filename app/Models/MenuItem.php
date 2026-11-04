<?php

namespace App\Models;

use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    /** @use HasFactory<MenuItemFactory> */
    use HasFactory;

    /**
     * الحقول القابلة للتعيين
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
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
            'is_available' => 'boolean',
        ];
    }

    /**
     * التصنيف الذي يتبعه الصنف
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * الإضافات المتاحة لهذا الصنف
     */
    public function addons(): HasMany
    {
        return $this->hasMany(Addon::class);
    }

    /**
     * نطاق الأصناف المتوفرة فقط للطلب
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /**
     * رابط صورة الصنف، مع صورة بديلة هادئة عند غياب الملف
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            $clean = ltrim($this->image, '/');
            if (file_exists(public_path('storage/' . $clean)) || Storage::disk('public')->exists($clean)) {
                return '/storage/' . $clean;
            }
            if (file_exists(public_path($clean))) {
                return '/' . $clean;
            }
        }

        return '/images/fallback-dish.svg';
    }

    /**
     * سعر الصنف بالريال السعودي
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0) . ' ر.س';
    }
}

