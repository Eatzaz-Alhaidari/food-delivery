<?php

namespace App\Livewire\Customer;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Review;
use App\Services\CartService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderTracker extends Component
{
    use AuthorizesRequests;

    public Order $order;
    public ?string $feedbackMessage = null;

    // حالة التقييم (المتطلب 1)
    public int $rating = 5;
    public string $comment = '';

    public function mount(int $order): void
    {
        $orderModel = Order::with(['restaurant', 'address', 'items.menuItem', 'statusLogs.changedBy', 'review'])
            ->findOrFail($order);

        // حماية Policy: العميل يرى طلباته فقط (المتطلب 8)
        $this->authorize('view', $orderModel);

        $this->order = $orderModel;
    }

    /**
     * تحديث تلقائي للبيانات وحالة الطلب كل 5 ثوانٍ
     */
    public function refreshOrder(): void
    {
        $this->order = Order::with(['restaurant', 'address', 'items.menuItem', 'statusLogs.changedBy', 'review'])
            ->findOrFail($this->order->id);
    }

    /**
     * إرسال تقييم الطلب بعد التسليم (المتطلب 1)
     */
    public function submitReview(): void
    {
        if (!$this->order->canBeReviewedByCustomer()) {
            $this->addError('review', 'لا يمكن تقييم هذا الطلب حالياً أو تم تقييمه مسبقاً.');
            return;
        }

        $validated = $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ], [
            'rating.required' => 'يرجى تحديد عدد النجوم من 1 إلى 5.',
            'rating.min' => 'أدنى تقييم هو نجمة واحدة.',
            'rating.max' => 'أعلى تقييم هو 5 نجوم.',
            'comment.max' => 'يجب ألا يتجاوز التعليق 500 حرف.',
        ]);

        Review::create([
            'order_id' => $this->order->id,
            'restaurant_id' => $this->order->restaurant_id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => !empty(trim($validated['comment'] ?? '')) ? trim($validated['comment']) : null,
        ]);

        $this->feedbackMessage = 'شكراً لك! تم تسجيل تقييمك للمطعم بنجاح.';
        $this->reset(['comment']);
        $this->rating = 5;
        $this->refreshOrder();
    }

    /**
     * إلغاء الطلب من جهة العميل (مسموح فقط قبل قبول المطعم له) (المتطلب 7)
     */
    public function cancelOrder(): void
    {
        $this->authorize('cancel', $this->order);

        if (!$this->order->canBeCancelledByCustomer()) {
            $this->addError('cancel', 'عذراً، لا يمكن إلغاء الطلب بعد أن بدأ المطعم في معالجته.');
            return;
        }

        try {
            $this->order->transitionTo(OrderStatus::Cancelled, Auth::user(), 'تم إلغاء الطلب من قبل العميل.');
            $this->feedbackMessage = 'تم إلغاء طلبك بنجاح.';
            $this->refreshOrder();
        } catch (\Throwable $e) {
            $this->addError('cancel', $e->getMessage());
        }
    }

    /**
     * إعادة إضافة أصناف الطلب إلى السلة والانتقال لصفحة إتمام الطلب (المتطلب 7)
     */
    public function reorder(CartService $cart)
    {
        $restaurant = $this->order->restaurant;
        if (!$restaurant || !$restaurant->is_active) {
            $this->addError('reorder', 'عذراً، هذا المطعم غير متاح للخدمة حالياً.');
            return null;
        }

        // تفريغ السلة للبدء من هذا المطعم
        $cart->clear();

        $addedCount = 0;
        foreach ($this->order->items as $item) {
            $menuItem = $item->menuItem;
            if ($menuItem && $menuItem->is_available) {
                $addonIds = [];
                if (!empty($item->addons_list)) {
                    $addonIds = array_column($item->addons_list, 'id');
                }
                $cart->add($menuItem, $item->quantity, $addonIds, null);
                $addedCount++;
            }
        }

        if ($addedCount === 0) {
            $this->addError('reorder', 'أصناف هذا الطلب غير متوفرة حالياً في قائمة المطعم.');
            return null;
        }

        $this->dispatch('cart-updated');
        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.customer.order-tracker')
            ->layout('layouts.app', ['title' => 'تتبع الطلب #' . $this->order->id]);
    }
}

