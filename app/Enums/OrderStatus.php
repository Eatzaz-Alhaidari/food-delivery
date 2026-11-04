<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    /**
     * اسم الحالة باللغة العربية
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'قيد الانتظار',
            self::Accepted => 'تم القبول',
            self::Preparing => 'قيد التجهيز',
            self::Ready => 'جاهز للتوصيل',
            self::OutForDelivery => 'في الطريق للتوصيل',
            self::Delivered => 'تم التسليم',
            self::Rejected => 'مرفوض',
            self::Cancelled => 'ملغي',
        };
    }

    /**
     * نمط شارة الحالة بحسب DESIGN.md
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Pending => 'neutral',
            self::Accepted => 'neutral',
            self::Preparing => 'solid-olive',
            self::Ready => 'solid-olive',
            self::OutForDelivery => 'primary',
            self::Delivered => 'primary',
            self::Rejected, self::Cancelled => 'accent',
        };
    }

    /**
     * أيقونة Lucide المناسبة للحالة
     */
    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'clock',
            self::Accepted => 'check',
            self::Preparing => 'utensils',
            self::Ready => 'package-check',
            self::OutForDelivery => 'truck',
            self::Delivered => 'check-circle-2',
            self::Rejected => 'x-circle',
            self::Cancelled => 'ban',
        };
    }

    /**
     * الحالة التالية المباشرة في مسار الطلب الطبيعي
     */
    public function nextState(): ?self
    {
        return match ($this) {
            self::Pending => self::Accepted,
            self::Accepted => self::Preparing,
            self::Preparing => self::Ready,
            self::Ready => self::OutForDelivery,
            self::OutForDelivery => self::Delivered,
            default => null,
        };
    }

    /**
     * الحالات المسموح بالانتقال إليها من الحالة الحالية (قاعدة منع القفز والرجوع)
     *
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Accepted, self::Rejected, self::Cancelled],
            self::Accepted => [self::Preparing],
            self::Preparing => [self::Ready],
            self::Ready => [self::OutForDelivery],
            self::OutForDelivery => [self::Delivered],
            self::Delivered, self::Rejected, self::Cancelled => [],
        };
    }

    /**
     * هل الانتقال للحالة المطلوبة مسموح به نظامياً؟
     */
    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    /**
     * هل الطلب في حالة نشطة جارية؟
     */
    public function isActive(): bool
    {
        return in_array($this, [
            self::Pending,
            self::Accepted,
            self::Preparing,
            self::Ready,
            self::OutForDelivery,
        ], true);
    }

    /**
     * هل الطلب في حالة نهائية مغلقة؟
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Delivered,
            self::Rejected,
            self::Cancelled,
        ], true);
    }

    /**
     * رقم الخطوة في شريط التتبع للعميل (1 إلى 5)
     */
    public function stepIndex(): int
    {
        return match ($this) {
            self::Pending => 1,
            self::Accepted, self::Preparing => 2,
            self::Ready => 3,
            self::OutForDelivery => 4,
            self::Delivered => 5,
            self::Rejected, self::Cancelled => 0,
        };
    }
}

