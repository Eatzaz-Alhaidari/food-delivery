<?php

namespace App\Models;

use Database\Factories\RestaurantFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

class Restaurant extends Model
{
    /** @use HasFactory<RestaurantFactory> */
    use HasFactory;

    /**
     * الحقول القابلة للتعيين
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'cuisine',
        'district',
        'logo',
        'cover',
        'min_order',
        'delivery_fee',
        'prep_time',
        'is_open',
        'is_active',
    ];

    /**
     * تحويلات الأنواع للخصائص
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_order' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'prep_time' => 'integer',
            'is_open' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * مفتاح التوجيه في المسارات هو الـ slug
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * علاقة المطعم بمالكه (مستخدم بدور restaurant)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة المطعم بتصنيفات قائمة الطعام
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('sort_order');
    }

    /**
     * علاقة المطعم بجميع أصناف المنيو عبر التصنيفات
     */
    public function menuItems(): HasManyThrough
    {
        return $this->hasManyThrough(MenuItem::class, Category::class);
    }

    /**
     * طلبات الزبائن المستلمة لهذا المطعم
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    /**
     * تقييمات العملاء لهذا المطعم
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    /**
     * متوسط تقييم المطعم من 5 نجوم (المتطلب 2)
     */
    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : 5.0;
    }

    /**
     * عدد التقييمات الإجمالي
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * نطاق استعلام المطاعم النشطة فقط
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق استعلام المطاعم المفتوحة التي تستقبل طلبات الآن
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_open', true);
    }

    /**
     * رابط شعار المطعم، مع صورة بديلة هادئة عند غياب الملف
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            $clean = ltrim($this->logo, '/');
            if (file_exists(public_path('storage/' . $clean)) || Storage::disk('public')->exists($clean)) {
                return '/storage/' . $clean;
            }
            if (file_exists(public_path($clean))) {
                return '/' . $clean;
            }
        }

        return '/images/fallback-restaurant.svg';
    }

    /**
     * رابط صورة غلاف المطعم، مع صورة بديلة هادئة عند غياب الملف
     */
    public function getCoverUrlAttribute(): string
    {
        if ($this->cover) {
            $clean = ltrim($this->cover, '/');
            if (file_exists(public_path('storage/' . $clean)) || Storage::disk('public')->exists($clean)) {
                return '/storage/' . $clean;
            }
            if (file_exists(public_path($clean))) {
                return '/' . $clean;
            }
        }

        return '/images/fallback-restaurant.svg';
    }

    /**
     * الحد الأدنى للطلب بصيغة الريال
     */
    public function getFormattedMinOrderAttribute(): string
    {
        return number_format($this->min_order, 0) . ' ر.س';
    }

    /**
     * رسوم التوصيل بصيغة الريال أو مجاني
     */
    public function getFormattedDeliveryFeeAttribute(): string
    {
        if ($this->delivery_fee <= 0) {
            return 'توصيل مجاني';
        }

        return number_format($this->delivery_fee, 0) . ' ر.س';
    }

    /**
     * وقت التجهيز المعرب
     */
    public function getFormattedPrepTimeAttribute(): string
    {
        return "{$this->prep_time} دقيقة";
    }
}

