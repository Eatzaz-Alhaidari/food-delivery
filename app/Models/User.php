<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * الحقول القابلة للتعبئة عبر Mass Assignment
     * ملاحظة أمنية: تم استبعاد 'role' لحمايته من أي محاولة تعيين خارجي
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * الحقول المخفية عند التحويل لمصفوفة أو JSON
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويلات الأنواع للخصائص
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * هل المستخدم مدير للمنصة؟
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * هل المستخدم صاحب أو ممثل مطعم؟
     */
    public function isRestaurant(): bool
    {
        return $this->role === UserRole::Restaurant;
    }

    /**
     * هل المستخدم عميل عادي؟
     */
    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer || $this->role === null;
    }

    /**
     * المطعم المرتبط بهذا المستخدم (إن كان دوره restaurant)
     */
    public function restaurant(): HasOne
    {
        return $this->hasOne(Restaurant::class);
    }

    /**
     * عناوين التوصيل المسجلة للعميل
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * الطلبات الصادرة من هذا المستخدم
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * التقييمات التي كتبها هذا المستخدم
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * مسار التوجيه بعد تسجيل الدخول حسب الدور
     */
    public function homeUrl(): string
    {
        return $this->role?->defaultRoute() ?? '/';
    }
}
