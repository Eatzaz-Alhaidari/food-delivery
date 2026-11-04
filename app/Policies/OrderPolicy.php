<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * هل يحق للمستخدم استعراض تفاصيل هذا الطلب؟
     * - مدير المنصة: يرى جميع الطلبات
     * - صاحب المطعم: يرى فقط الطلبات المحالة لمطعمه
     * - العميل: يرى فقط طلباته الخاصة
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isRestaurant()) {
            return $user->restaurant !== null && (int) $order->restaurant_id === (int) $user->restaurant->id;
        }

        return (int) $order->user_id === (int) $user->id;
    }

    /**
     * هل يحق للمستخدم تحديث حالة الطلب؟
     * - صاحب المطعم الخاص بهذا الطلب فقط (أو المدير)
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isRestaurant()) {
            return $user->restaurant !== null && (int) $order->restaurant_id === (int) $user->restaurant->id;
        }

        return false;
    }

    /**
     * هل يحق للعميل إلغاء الطلب؟
     * - فقط للعميل صاحب الطلب، وطالما كان الطلب في حالة pending (قبل القبول)
     */
    public function cancel(User $user, Order $order): bool
    {
        return (int) $order->user_id === (int) $user->id && $order->canBeCancelledByCustomer();
    }
}

