<?php

namespace App\Livewire\Customer;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $filter = 'all'; // all, active, completed
    public ?string $feedbackMessage = null;

    /**
     * إلغاء الطلب من جهة العميل (مسموح فقط قبل قبول المطعم له)
     */
    public function cancelOrder(int $orderId): void
    {
        $order = Auth::user()->orders()->findOrFail($orderId);
        $this->authorize('cancel', $order);

        if (! $order->canBeCancelledByCustomer()) {
            $this->addError('order_action', 'عذراً، لا يمكن إلغاء الطلب بعد أن قبله المطعم وبدأ في التجهيز.');
            return;
        }

        try {
            $order->transitionTo(OrderStatus::Cancelled, Auth::user(), 'تم إلغاء الطلب من قِبل العميل.');
            $this->feedbackMessage = "تم إلغاء الطلب #{$order->id} بنجاح.";
        } catch (\Throwable $e) {
            $this->addError('order_action', $e->getMessage());
        }
    }

    /**
     * إعادة الطلب: تعبئة السلة بأصناف هذا الطلب والانتقال للدفع (المتطلب 7)
     */
    public function reorder(int $orderId, CartService $cart)
    {
        $order = Auth::user()->orders()->with('items.menuItem.addons')->findOrFail($orderId);

        $restaurant = $order->restaurant;
        if (! $restaurant || ! $restaurant->is_active) {
            $this->addError('order_action', 'عذراً، هذا المطعم غير متاح للخدمة حالياً.');
            return null;
        }

        // تفريغ السلة للبدء من هذا المطعم
        $cart->clear();

        $addedCount = 0;
        foreach ($order->items as $item) {
            $menuItem = $item->menuItem;
            if ($menuItem && $menuItem->is_available) {
                $addonIds = [];
                if (! empty($item->addons_list)) {
                    $addonIds = array_column($item->addons_list, 'id');
                }
                $cart->add($menuItem, $item->quantity, $addonIds, null);
                $addedCount++;
            }
        }

        if ($addedCount === 0) {
            $this->addError('order_action', 'أصناف هذا الطلب غير متوفرة حالياً في قائمة المطعم.');
            return null;
        }

        $this->dispatch('cart-updated');
        return redirect()->route('checkout');
    }

    public function render()
    {
        // استعلام طلبات هذا العميل فقط (المتطلب 8: العميل يرى طلباته فقط)
        $query = Auth::user()->orders()
            ->with(['restaurant', 'items'])
            ->latest();

        if ($this->filter === 'active') {
            $query->whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Accepted->value,
                OrderStatus::Preparing->value,
                OrderStatus::Ready->value,
                OrderStatus::OutForDelivery->value,
            ]);
        } elseif ($this->filter === 'completed') {
            $query->whereIn('status', [
                OrderStatus::Delivered->value,
                OrderStatus::Rejected->value,
                OrderStatus::Cancelled->value,
            ]);
        }

        $orders = $query->paginate(8);

        return view('livewire.customer.order-list', [
            'orders' => $orders,
        ])->layout('layouts.app', ['title' => 'طلباتي']);
    }
}

