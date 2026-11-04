<?php

namespace App\Livewire\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\PlatformSetting;
use App\Models\Restaurant;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $today = Carbon::today();

        // ١. استعلامات الإحصائيات الحقيقية من قاعدة البيانات (المتطلب 1)
        $todayOrdersCount = Order::whereDate('created_at', $today)->count();

        // إجمالي المبيعات المحققة للطلبات المسلّمة فقط (حسب المتطلب 1)
        $totalDeliveredSales = (float) Order::where('status', OrderStatus::Delivered->value)->sum('total');

        // مبيعات اليوم المسلّمة
        $todayDeliveredSales = (float) Order::where('status', OrderStatus::Delivered->value)
            ->whereDate('created_at', $today)
            ->sum('total');

        // المطاعم النشطة وإجمالي المطاعم
        $activeRestaurantsCount = Restaurant::where('is_active', true)->count();
        $totalRestaurantsCount = Restaurant::count();

        // عمولة المنصة
        $commissionRate = PlatformSetting::getCommissionPercentage();
        $estimatedCommission = round($totalDeliveredSales * ($commissionRate / 100), 2);

        // ٢. بيانات الرسم البياني للطلبات في آخر 7 أيام (المتطلب 2)
        $last7Days = collect();
        $dayNamesAr = [
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayEnglish = $date->format('l');
            $count = Order::whereDate('created_at', $date)->count();
            $sales = (float) Order::where('status', OrderStatus::Delivered->value)
                ->whereDate('created_at', $date)
                ->sum('total');

            $last7Days->push([
                'date' => $date->format('Y-m-d'),
                'day_label' => $dayNamesAr[$dayEnglish] ?? $date->format('D'),
                'count' => $count,
                'sales' => $sales,
            ]);
        }

        $maxOrders = max($last7Days->max('count'), 1);

        // ٣. قسم أكثر المطاعم مبيعاً (المتطلب 5)
        $topRestaurants = Restaurant::withCount(['orders as delivered_orders_count' => function ($q) {
            $q->where('status', OrderStatus::Delivered->value);
        }])
        ->withSum(['orders as total_sales' => function ($q) {
            $q->where('status', OrderStatus::Delivered->value);
        }], 'total')
        ->orderByDesc('total_sales')
        ->take(5)
        ->get();

        // ٤. جدول آخر الطلبات في المنصة (المتطلب 1)
        $recentOrders = Order::with(['restaurant', 'user'])
            ->latest()
            ->take(6)
            ->get();

        return view('livewire.admin.dashboard', [
            'todayOrdersCount' => $todayOrdersCount,
            'todayDeliveredSales' => $todayDeliveredSales,
            'totalDeliveredSales' => $totalDeliveredSales,
            'activeRestaurantsCount' => $activeRestaurantsCount,
            'totalRestaurantsCount' => $totalRestaurantsCount,
            'commissionRate' => $commissionRate,
            'estimatedCommission' => $estimatedCommission,
            'last7Days' => $last7Days,
            'maxOrders' => $maxOrders,
            'topRestaurants' => $topRestaurants,
            'recentOrders' => $recentOrders,
        ])->layout('layouts.admin', ['title' => 'لوحة المؤشرات']);
    }
}

