<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Admin\CouponManager as AdminCouponManager;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\OrderManager as AdminOrderManager;
use App\Livewire\Admin\RestaurantManager as AdminRestaurantManager;
use App\Livewire\Admin\SalesReport as AdminSalesReport;
use App\Livewire\Admin\SettingsManager as AdminSettingsManager;
use App\Livewire\Customer\AddressManager;
use App\Livewire\Customer\Checkout;
use App\Livewire\Customer\OrderConfirmation;
use App\Livewire\Customer\OrderList;
use App\Livewire\Customer\OrderTracker;
use App\Livewire\Customer\RestaurantDetail;
use App\Livewire\Customer\RestaurantList;
use App\Livewire\Restaurant\MenuManager;
use App\Livewire\Restaurant\Orders;
use App\Livewire\Restaurant\Profile as RestaurantProfile;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Route;

// الصفحة الرئيسية (الواجهة العامة للعملاء)
Route::get('/', function () {
    $restaurants = Restaurant::active()
        ->orderBy('is_open', 'desc')
        ->take(8)
        ->get();

    return view('welcome', compact('restaurants'));
})->name('home');

// تصفح المطاعم مع الفلترة والبحث (Livewire)
Route::get('/restaurants', RestaurantList::class)->name('restaurants.index');

// تفاصيل المطعم وقائمة الطعام (المنيو مقسماً بالتصنيفات مع شريط ثابت)
Route::get('/restaurants/{slug}', RestaurantDetail::class)->name('restaurants.show');

// إدارة عناوين العميل (Livewire)
Route::get('/addresses', AddressManager::class)
    ->middleware(['auth', 'role:customer'])
    ->name('addresses.index');

// صفحة إتمام الطلب (Livewire)
Route::get('/checkout', Checkout::class)
    ->middleware(['auth', 'role:customer'])
    ->name('checkout');

// صفحة تأكيد استلام الطلب
Route::get('/orders/{order}/confirmation', OrderConfirmation::class)
    ->middleware(['auth'])
    ->name('orders.confirmation');

// قائمة طلباتي للعميل (Livewire)
Route::get('/orders', OrderList::class)
    ->middleware(['auth', 'role:customer'])
    ->name('orders.index');

// صفحة تتبع الطلب التفاعلية بشريط المراحل الحي (Livewire)
Route::get('/orders/{order}/track', OrderTracker::class)
    ->middleware(['auth'])
    ->name('orders.track');

// توجيه لوحة التحكم الافتراضية بحسب دور المستخدم المسجل
Route::get('/dashboard', function () {
    return redirect(auth()->user()->homeUrl());
})->middleware(['auth'])->name('dashboard');

// لوحة إدارة المطعم (محمية بـ role:restaurant ومربوطة بـ Livewire و Policy)
Route::middleware(['auth', 'role:restaurant'])->prefix('restaurant')->name('restaurant.')->group(function () {
    Route::get('/', MenuManager::class)->name('dashboard');
    Route::get('/menu', MenuManager::class)->name('menu');
    Route::get('/profile', RestaurantProfile::class)->name('profile');
    Route::get('/orders', Orders::class)->name('orders');
});

// لوحة إدارة المنصة (محمية بـ role:admin ومربوطة بـ Livewire بالكامل)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/restaurants', AdminRestaurantManager::class)->name('restaurants');
    Route::get('/orders', AdminOrderManager::class)->name('orders');
    Route::get('/coupons', AdminCouponManager::class)->name('coupons');
    Route::get('/sales', AdminSalesReport::class)->name('sales');
    Route::get('/settings', AdminSettingsManager::class)->name('settings');
});

// إعدادات الملف الشخصي للعملاء والمستخدمين
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

