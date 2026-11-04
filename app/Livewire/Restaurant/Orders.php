<?php

namespace App\Livewire\Restaurant;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Orders extends Component
{
    use AuthorizesRequests;

    public Restaurant $restaurant;

    // تصفية الحالات
    public string $statusFilter = 'all'; // all, pending, active, completed

    // معاينة تفاصيل طلب محدد
    public ?int $selectedOrderId = null;
    public ?Order $selectedOrder = null;

    // نافذة رفض الطلب مع السبب الإلزامي
    public bool $showRejectModal = false;
    public ?int $rejectOrderId = null;
    public string $rejectReason = '';

    // متابعة وصول طلبات جديدة للتنبيه الخفيف
    public int $lastKnownPendingCount = 0;
    public bool $hasNewOrderAlert = false;

    public ?string $feedbackMessage = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->restaurant = $user->restaurant ?? abort(403, 'لا يوجد مطعم مرتبط بحسابك.');
        $this->authorize('view', $this->restaurant);

        $this->lastKnownPendingCount = $this->restaurant->orders()
            ->where('status', OrderStatus::Pending->value)
            ->count();
    }

    /**
     * تحديث تلقائي كل 10 ثوانٍ لفحص وصول أي طلبات جديدة
     */
    public function pollOrders(): void
    {
        $currentPending = $this->restaurant->orders()
            ->where('status', OrderStatus::Pending->value)
            ->count();

        if ($currentPending > $this->lastKnownPendingCount) {
            $this->hasNewOrderAlert = true;
        }

        $this->lastKnownPendingCount = $currentPending;

        // إذا كان هناك طلب معروض، يتم تحديث بياناته
        if ($this->selectedOrderId) {
            $this->selectedOrder = $this->restaurant->orders()
                ->with(['items', 'address', 'user', 'statusLogs.changedBy'])
                ->find($this->selectedOrderId);
        }
    }

    public function dismissNewOrderAlert(): void
    {
        $this->hasNewOrderAlert = false;
    }

    /**
     * الانتقال إلى الحالة التالية المسموح بها فقط وفق قاعدة الدولة الصارمة
     */
    public function advanceStatus(int $orderId): void
    {
        $order = $this->restaurant->orders()->findOrFail($orderId);
        $this->authorize('update', $order);

        $nextStatus = $order->nextAvailableStatus();

        if (!$nextStatus) {
            $this->addError('order_action', 'هذا الطلب وصل إلى أقصى حالة ولا يمكن ترقيته.');
            return;
        }

        try {
            $order->transitionTo($nextStatus, auth()->user(), 'تحديث الحالة من لوحة إدارة المطعم.');
            $this->feedbackMessage = "تم تحديث الطلب #{$order->id} إلى حالة: \"{$nextStatus->label()}\".";

            if ($this->selectedOrderId === $order->id) {
                $this->showOrderDetails($order->id);
            }
        } catch (\Throwable $e) {
            $this->addError('order_action', $e->getMessage());
        }
    }

    /**
     * فتح نافذة الرفض مع اشتراط كتابة السبب
     */
    public function openRejectModal(int $orderId): void
    {
        $order = $this->restaurant->orders()->findOrFail($orderId);
        $this->authorize('update', $order);

        if (!$order->canBeRejectedByRestaurant()) {
            $this->addError('order_action', 'لا يمكن رفض هذا الطلب لأنه بدأ بمراحل التجهيز.');
            return;
        }

        $this->rejectOrderId = $order->id;
        $this->rejectReason = '';
        $this->resetValidation();
        $this->showRejectModal = true;
    }

    /**
     * تأكيد رفض الطلب مع السبب الإلزامي
     */
    public function submitReject(): void
    {
        $this->validate([
            'rejectReason' => 'required|string|min:4|max:500',
        ], [
            'rejectReason.required' => 'سبب رفض الطلب إلزامي لإشعار العميل بسبب عدم قبول طلبه.',
            'rejectReason.min' => 'يرجى كتابة سبب واضح لا يقل عن 4 أحرف.',
        ]);

        $order = $this->restaurant->orders()->findOrFail($this->rejectOrderId);
        $this->authorize('update', $order);

        try {
            $order->transitionTo(OrderStatus::Rejected, auth()->user(), $this->rejectReason);
            $this->feedbackMessage = "تم رفض الطلب #{$order->id} وحفظ السبب في السجل.";

            $this->showRejectModal = false;
            $this->rejectOrderId = null;
            $this->rejectReason = '';

            if ($this->selectedOrderId === $order->id) {
                $this->showOrderDetails($order->id);
            }
        } catch (\Throwable $e) {
            $this->addError('rejectReason', $e->getMessage());
        }
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectOrderId = null;
        $this->rejectReason = '';
        $this->resetValidation();
    }

    /**
     * عرض تفاصيل الطلب الكاملة وسجل الحركات
     */
    public function showOrderDetails(int $orderId): void
    {
        $order = $this->restaurant->orders()
            ->with(['items', 'address', 'user', 'statusLogs.changedBy'])
            ->findOrFail($orderId);

        $this->authorize('view', $order);

        $this->selectedOrderId = $order->id;
        $this->selectedOrder = $order;
    }

    public function closeOrderDetails(): void
    {
        $this->selectedOrderId = null;
        $this->selectedOrder = null;
    }

    public function render()
    {
        // استعلام الطلبات الخاصة بهذا المطعم فقط (حماية Policy وعزل المستأجرين)
        $query = $this->restaurant->orders()
            ->with(['items', 'address', 'user'])
            ->latest();

        if ($this->statusFilter === 'pending') {
            $query->where('status', OrderStatus::Pending->value);
        } elseif ($this->statusFilter === 'active') {
            $query->whereIn('status', [
                OrderStatus::Accepted->value,
                OrderStatus::Preparing->value,
                OrderStatus::Ready->value,
                OrderStatus::OutForDelivery->value,
            ]);
        } elseif ($this->statusFilter === 'completed') {
            $query->whereIn('status', [
                OrderStatus::Delivered->value,
                OrderStatus::Rejected->value,
                OrderStatus::Cancelled->value,
            ]);
        }

        $orders = $query->paginate(12);

        $pendingCount = $this->restaurant->orders()
            ->where('status', OrderStatus::Pending->value)
            ->count();

        $activeCount = $this->restaurant->orders()
            ->whereIn('status', [
                OrderStatus::Accepted->value,
                OrderStatus::Preparing->value,
                OrderStatus::Ready->value,
                OrderStatus::OutForDelivery->value,
            ])
            ->count();

        return view('livewire.restaurant.orders', [
            'orders' => $orders,
            'pendingCount' => $pendingCount,
            'activeCount' => $activeCount,
        ])->layout('layouts.restaurant', ['title' => 'الطلبات الواردة']);
    }
}
