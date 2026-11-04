<?php

namespace App\Livewire\Admin;

use App\Enums\OrderStatus;
use App\Models\PlatformSetting;
use App\Models\Restaurant;
use Livewire\Component;

class SalesReport extends Component
{
    public function render()
    {
        $commissionRate = PlatformSetting::getCommissionPercentage();

        // جلب جميع المطاعم مع إجمالي المبيعات المحققة للطلبات المسلمة فقط
        $restaurants = Restaurant::withCount(['orders as delivered_orders_count' => function ($q) {
            $q->where('status', OrderStatus::Delivered->value);
        }])
        ->withSum(['orders as total_sales' => function ($q) {
            $q->where('status', OrderStatus::Delivered->value);
        }], 'total')
        ->orderByDesc('total_sales')
        ->get();

        // الحسابات الإجمالية للمنصة
        $totalSalesAll = 0.0;
        $totalCommissionAll = 0.0;
        $totalNetPayoutAll = 0.0;

        foreach ($restaurants as $r) {
            $sales = (float) ($r->total_sales ?? 0);
            $commission = round($sales * ($commissionRate / 100), 2);
            $net = $sales - $commission;

            $r->calculated_commission = $commission;
            $r->calculated_net = $net;

            $totalSalesAll += $sales;
            $totalCommissionAll += $commission;
            $totalNetPayoutAll += $net;
        }

        return view('livewire.admin.sales-report', [
            'restaurants' => $restaurants,
            'commissionRate' => $commissionRate,
            'totalSalesAll' => $totalSalesAll,
            'totalCommissionAll' => $totalCommissionAll,
            'totalNetPayoutAll' => $totalNetPayoutAll,
        ])->layout('layouts.admin', ['title' => 'المبيعات وعمولة المنصة']);
    }
}

