<?php

namespace App\Livewire\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Restaurant;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public string $restaurantFilter = 'all';
    public string $dateFilter = 'all'; // all, today, week, month

    public ?int $selectedOrderId = null;
    public ?Order $selectedOrder = null;

    public function showDetails(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
        $this->selectedOrder = Order::with(['restaurant', 'user', 'address', 'items', 'statusLogs.changedBy'])
            ->findOrFail($orderId);
    }

    public function closeDetails(): void
    {
        $this->selectedOrderId = null;
        $this->selectedOrder = null;
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'restaurantFilter', 'dateFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::with(['restaurant', 'user', 'address', 'items'])
            ->latest();

        // فلترة بالبحث
        if (! empty(trim($this->search))) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('id', 'like', $term)
                    ->orWhereHas('user', function ($uq) use ($term) {
                        $uq->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term);
                    })
                    ->orWhereHas('restaurant', function ($rq) use ($term) {
                        $rq->where('name', 'like', $term);
                    });
            });
        }

        // فلترة بالحالة
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // فلترة بالمطعم
        if ($this->restaurantFilter !== 'all') {
            $query->where('restaurant_id', (int) $this->restaurantFilter);
        }

        // فلترة بالتاريخ
        if ($this->dateFilter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($this->dateFilter === 'week') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($this->dateFilter === 'month') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        $orders = $query->paginate(12);
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);

        return view('livewire.admin.order-manager', [
            'orders' => $orders,
            'restaurants' => $restaurants,
        ])->layout('layouts.admin', ['title' => 'كافة الطلبات']);
    }
}

