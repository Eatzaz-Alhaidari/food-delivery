<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderConfirmation extends Component
{
    public Order $order;

    public function mount(int $order): void
    {
        $orderModel = Order::with(['restaurant', 'address', 'items'])->findOrFail($order);

        // حماية الخصوصية: لا يمكن لعميل رؤية تفاصيل طلب عميل آخر
        if ($orderModel->user_id !== Auth::id() && ! Auth::user()?->isAdmin()) {
            abort(403, 'غير مصرح لك باستعراض تفاصيل هذا الطلب.');
        }

        $this->order = $orderModel;
    }

    public function render()
    {
        return view('livewire.customer.order-confirmation')
            ->layout('layouts.app', ['title' => 'تأكيد الطلب #' . $this->order->id]);
    }
}

