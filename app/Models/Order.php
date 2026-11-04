<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعيين
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'address_id',
        'coupon_id',
        'status',
        'subtotal',
        'delivery_fee',
        'discount_amount',
        'total',
        'payment_method',
        'notes',
    ];

    /**
     * تحويلات الأنواع
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * العميل صاحب الطلب
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * المطعم المحال له الطلب
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * عنوان التوصيل
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * كوبون الخصم المستخدم إن وُجد
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * تقييم العميل لهذا الطلب
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * هل يحق للعميل تقييم هذا الطلب؟
     * مسموح فقط إذا كان الطلب مسلّماً (delivered) ولم يتم تقييمه مسبقاً (المتطلب 1)
     */
    public function canBeReviewedByCustomer(): bool
    {
        return $this->status === OrderStatus::Delivered && ! $this->review()->exists();
    }

    /**
     * تفاصيل أصناف الطلب
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * سجل تغيرات حالات الطلب
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

    /**
     * نطاق استعلام الطلبات النشطة الجارية
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::Pending->value,
            OrderStatus::Accepted->value,
            OrderStatus::Preparing->value,
            OrderStatus::Ready->value,
            OrderStatus::OutForDelivery->value,
        ]);
    }

    /**
     * تنفيذ انتقال الحالة مع فرض قاعدة منع القفز والرجوع (المتطلب 2)
     * وتسجيل كل حركة في order_status_logs (المتطلب 3)
     *
     * @throws \DomainException إذا كان الانتقال غير مسموح به
     * @throws \InvalidArgumentException إذا كان الرفض بدون سبب
     */
    public function transitionTo(OrderStatus $targetStatus, ?User $byUser = null, ?string $comment = null): void
    {
        $currentStatus = $this->status;

        // ١. فحص قاعدة الانتقال الصارمة (منع القفز أو الرجوع للخلف)
        if (! $currentStatus->canTransitionTo($targetStatus)) {
            $allowedNames = implode('، ', array_map(fn ($s) => $s->label(), $currentStatus->allowedTransitions()));
            $allowedText = empty($allowedNames) ? 'لا يمكن تغيير حالة هذا الطلب لأنه في حالة نهائية' : "الحالات المتاحة فقط: {$allowedNames}";

            throw new \DomainException(
                "غير مسموح بالانتقال من حالة \"{$currentStatus->label()}\" إلى حالة \"{$targetStatus->label()}\". {$allowedText}."
            );
        }

        // ٢. اشتراط سبب إلزامي عند رفض الطلب (المتطلب 4)
        if ($targetStatus === OrderStatus::Rejected && empty(trim($comment ?? ''))) {
            throw new \InvalidArgumentException('سبب رفض الطلب إلزامي لإبلاغ العميل بوضوح.');
        }

        // ٣. تنفيذ التحديث وحفظ السجل التاريخي داخل معاملة متكاملة
        DB::transaction(function () use ($currentStatus, $targetStatus, $byUser, $comment) {
            $this->update(['status' => $targetStatus]);

            $this->statusLogs()->create([
                'from_status' => $currentStatus,
                'to_status' => $targetStatus,
                'changed_by_user_id' => $byUser?->id,
                'comment' => $comment ? trim($comment) : null,
            ]);
        });
    }

    /**
     * هل يمكن للعميل إلغاء الطلب؟
     * مسموح فقط طالما الطلب ما زال قيد الانتظار (قبل القبول) بحسب المتطلب 7
     */
    public function canBeCancelledByCustomer(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    /**
     * هل يمكن للمطعم رفض الطلب؟ (فقط في مرحلة pending)
     */
    public function canBeRejectedByRestaurant(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    /**
     * الحالة التالية المباشرة للطلب
     */
    public function nextAvailableStatus(): ?OrderStatus
    {
        return $this->status->nextState();
    }

    /**
     * اسم الحالة بالعربية
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status instanceof OrderStatus
            ? $this->status->label()
            : OrderStatus::tryFrom((string) $this->status)?->label() ?? (string) $this->status;
    }

    /**
     * لون الشارة بحسب الحالة
     */
    public function getStatusBadgeVariantAttribute(): string
    {
        return $this->status instanceof OrderStatus
            ? $this->status->badgeVariant()
            : OrderStatus::tryFrom((string) $this->status)?->badgeVariant() ?? 'neutral';
    }

    /**
     * طريقة الدفع بالعربية
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'الدفع نقدياً عند الاستلام',
            'card_mock' => 'دفع إلكتروني تجريبي (مدى / بطاقة ائتمانية)',
            default => $this->payment_method,
        };
    }

    /**
     * المجموع الفرعي منسقاً
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2) . ' ر.س';
    }

    /**
     * رسوم التوصيل منسقة
     */
    public function getFormattedDeliveryFeeAttribute(): string
    {
        return number_format($this->delivery_fee, 2) . ' ر.س';
    }

    /**
     * مبلغ الخصم منسقاً
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return number_format($this->discount_amount, 2) . ' ر.س';
    }

    /**
     * الإجمالي النهائي منسقاً
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2) . ' ر.س';
    }
}
