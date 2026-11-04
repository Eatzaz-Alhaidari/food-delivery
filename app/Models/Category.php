<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /**
     * الحقول القابلة للتعيين
     *
     * @var list<string>
     */
    protected $fillable = [
        'restaurant_id',
        'name',
        'sort_order',
    ];

    /**
     * تحويلات الأنواع
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * المطعم الذي يتبعه هذا التصنيف
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * الأصناف التابعة لهذا التصنيف
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}

