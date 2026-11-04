<?php

namespace App\Livewire\Customer;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class Checkout extends Component
{
    public ?int $selectedAddressId = null;
    public string $paymentMethod = 'cash';
    public ?string $orderNotes = '';

    // حالة إضافة عنوان جديد بشكل سريع أثناء الدفع
    public bool $showNewAddressModal = false;
    public string $newTitle = '';
    public string $newDistrict = '';
    public string $newStreet = '';
    public string $newDetails = '';

    // حالة الكوبون والخصم (المتطلب 5 و 6 و 7)
    public string $couponCode = '';
    public ?Coupon $appliedCoupon = null;
    public float $discountAmount = 0.0;
    public ?string $couponSuccessMessage = null;

    // حماية منع الضغط المزدوج
    public bool $isSubmitting = false;

    /**
     * التهيئة والتحقق الأولي من السلة والعناوين
     */
    public function mount(CartService $cart): mixed
    {
        // ١. منع الدخول إذا كانت السلة فارغة
        if ($cart->isEmpty()) {
            return redirect()->route('restaurants.index');
        }

        // ٢. فحص صلاحية السلة والحد الأدنى وحالة المطعم
        $validationErrors = $cart->validateForCheckout();
        if (! empty($validationErrors)) {
            session()->flash('cart_error', $validationErrors[0]);
            return redirect()->route('restaurants.index');
        }

        // ٣. تعيين آخر عنوان مسجل تلقائياً لتسهيل تجربة المستخدم
        $latestAddress = Auth::user()->addresses()->latest()->first();
        if ($latestAddress) {
            $this->selectedAddressId = $latestAddress->id;
        }

        return null;
    }

    /**
     * تطبيق كود الكوبون مع الحماية بـ Rate Limiting (المتطلب 5 و 7)
     */
    public function applyCoupon(CartService $cart): void
    {
        $this->resetErrorBag('coupon');
        $this->couponSuccessMessage = null;

        // Rate Limiting: حد أقصى 5 محاولات في الدقيقة لمنع التخمين (المتطلب 7)
        $throttleKey = 'coupon-attempt:' . Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('coupon', "تم تجاوز عدد المحاولات المسموح بها للكوبونات. يرجى الانتظار {$seconds} ثانية.");
            return;
        }

        $code = strtoupper(trim($this->couponCode));
        if (empty($code)) {
            $this->addError('coupon', 'يرجى إدخال كود الكوبون أولاً.');
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon) {
            $this->addError('coupon', 'كود الكوبون المدخل غير صحيح أو غير موجود.');
            return;
        }

        $subtotal = $cart->subtotal();
        $validation = $coupon->validateForSubtotal($subtotal);
        if (! $validation['valid']) {
            $this->addError('coupon', $validation['message']);
            return;
        }

        // احتساب الخصم ومنع أن يصير الإجمالي سالباً (المتطلب 5)
        $discount = $coupon->calculateDiscount($subtotal);

        $this->appliedCoupon = $coupon;
        $this->discountAmount = $discount;
        $this->couponSuccessMessage = "تم تطبيق الكوبون \"{$coupon->code}\" بنجاح (- " . number_format($discount, 2) . ' ر.س).';
    }

    /**
     * إزالة الكوبون المطبق
     */
    public function removeCoupon(): void
    {
        $this->appliedCoupon = null;
        $this->discountAmount = 0.0;
        $this->couponCode = '';
        $this->couponSuccessMessage = null;
        $this->resetErrorBag('coupon');
    }

    /**
     * حفظ عنوان جديد سريع من داخل صفحة إتمام الطلب
     */
    public function saveNewAddress(): void
    {
        $validated = $this->validate([
            'newTitle' => 'required|string|min:2|max:60',
            'newDistrict' => 'required|string|min:2|max:100',
            'newStreet' => 'required|string|min:2|max:100',
            'newDetails' => 'nullable|string|max:500',
        ], [
            'newTitle.required' => 'حقل تسمية العنوان مطلوب.',
            'newDistrict.required' => 'حقل اسم الحي مطلوب.',
            'newStreet.required' => 'حقل اسم الشارع مطلوب.',
        ]);

        $address = Auth::user()->addresses()->create([
            'title' => $validated['newTitle'],
            'district' => $validated['newDistrict'],
            'street' => $validated['newStreet'],
            'details' => $validated['newDetails'] ?? null,
        ]);

        $this->selectedAddressId = $address->id;
        $this->showNewAddressModal = false;
        $this->reset(['newTitle', 'newDistrict', 'newStreet', 'newDetails']);
    }

    /**
     * إرسال وإنشاء الطلب داخل DB::transaction مع تطبيق الخصم وزيادة استهلاك الكوبون
     */
    public function submitOrder(CartService $cart)
    {
        // ١. منع تكرار الإرسال بالضغط المزدوج
        if ($this->isSubmitting) {
            return null;
        }

        $this->isSubmitting = true;

        // ٢. Rate Limiting على إنشاء الطلبات
        $throttleKey = 'place-order:' . Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('general', "تم إرسال عدة طلبات مؤخراً. يرجى الانتظار {$seconds} ثانية والمحاولة مجدداً.");
            $this->isSubmitting = false;
            return null;
        }

        // ٣. التحقق من صحة المدخلات برسائل عربية واضحة
        $this->validate([
            'selectedAddressId' => 'required|exists:addresses,id',
            'paymentMethod' => 'required|in:cash,card_mock',
            'orderNotes' => 'nullable|string|max:1000',
        ], [
            'selectedAddressId.required' => 'يرجى اختيار أو إضافة عنوان توصيل لمتابعة الطلب.',
            'selectedAddressId.exists' => 'عنوان التوصيل المختار غير صحيح.',
            'paymentMethod.required' => 'يرجى تحديد طريقة الدفع المفضلة.',
            'paymentMethod.in' => 'طريقة الدفع المختارة غير مقبولة.',
        ]);

        $address = Auth::user()->addresses()->where('id', $this->selectedAddressId)->first();
        if (! $address) {
            $this->addError('selectedAddressId', 'عنوان التوصيل المحدد لا ينتمي لحسابك.');
            $this->isSubmitting = false;
            return null;
        }

        // ٤. إعادة التحقق اللحظي الحاسم من DB: السلة، المطعم مفتوح، الأصناف متوفرة، الحد الأدنى
        $checkoutErrors = $cart->validateForCheckout();
        if (! empty($checkoutErrors)) {
            $this->addError('general', $checkoutErrors[0]);
            $this->isSubmitting = false;
            return null;
        }

        RateLimiter::hit($throttleKey, 60);

        try {
            // ٥. تنفيذ حفظ الطلب وأصنافه والكوبون داخل DB::transaction لضمان الذرية (المتطلب 6)
            $order = DB::transaction(function () use ($cart, $address) {
                $restaurant = $cart->getRestaurant();
                $items = $cart->getItems();
                $subtotal = $cart->subtotal();
                $deliveryFee = $cart->deliveryFee();

                // إعادة احتساب الكوبون طازجاً من DB داخل الـ Transaction لضمان الأمان
                $finalCoupon = null;
                $finalDiscount = 0.0;

                if ($this->appliedCoupon) {
                    $dbCoupon = Coupon::where('id', $this->appliedCoupon->id)->lockForUpdate()->first();
                    if ($dbCoupon && $dbCoupon->validateForSubtotal($subtotal)['valid']) {
                        $finalCoupon = $dbCoupon;
                        $finalDiscount = $dbCoupon->calculateDiscount($subtotal);

                        // زيادة عدد مرات الاستخدام بشكل آمن (المتطلب 6)
                        $dbCoupon->increment('used_count');
                    }
                }

                // حساب الإجمالي النهائي ومنع أن يصبح سالباً
                $total = max(0, $subtotal - $finalDiscount) + $deliveryFee;

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'restaurant_id' => $restaurant->id,
                    'address_id' => $address->id,
                    'coupon_id' => $finalCoupon?->id,
                    'status' => OrderStatus::Pending,
                    'subtotal' => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'discount_amount' => $finalDiscount,
                    'total' => $total,
                    'payment_method' => $this->paymentMethod,
                    'notes' => $this->orderNotes ? trim($this->orderNotes) : null,
                ]);

                // حفظ الأصناف بلقطة السعر والاسم وقت الشراء
                foreach ($items as $item) {
                    $addonsSnapshot = $item->addons->map(function ($addon) {
                        return [
                            'id' => $addon->id,
                            'name' => $addon->name,
                            'price' => (float) $addon->extra_price,
                        ];
                    })->values()->toArray();

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $item->menu_item->id,
                        'name' => $item->menu_item->name,
                        'price' => $item->unit_base_price,
                        'quantity' => $item->quantity,
                        'addons_json' => ! empty($addonsSnapshot) ? $addonsSnapshot : null,
                    ]);
                }

                // تسجيل الحالة الأولى في سجل حركة الطلب
                $order->statusLogs()->create([
                    'from_status' => null,
                    'to_status' => OrderStatus::Pending,
                    'changed_by_user_id' => Auth::id(),
                    'comment' => 'تم إنشاء الطلب بنجاح من العميل وبانتظار مراجعة المطعم.',
                ]);

                // تفريغ السلة بعد نجاح الحفظ
                $cart->clear();

                return $order;
            });

            $this->dispatch('cart-updated');

            return redirect()->route('orders.confirmation', $order->id);
        } catch (\Throwable $e) {
            $this->isSubmitting = false;
            $this->addError('general', 'حدث خطأ أثناء معالجة الطلب، يرجى المحاولة مرة أخرى.');
            return null;
        }
    }

    public function render(CartService $cart)
    {
        $subtotal = $cart->subtotal();
        $deliveryFee = $cart->deliveryFee();

        // إعادة حساب الخصم ديناميكياً والتأكد من عدم جعل الإجمالي سالباً
        $discount = 0.0;
        if ($this->appliedCoupon) {
            $discount = $this->appliedCoupon->calculateDiscount($subtotal);
        }

        $total = max(0, $subtotal - $discount) + $deliveryFee;
        $addresses = Auth::user()->addresses()->latest()->get();

        return view('livewire.customer.checkout', [
            'restaurant' => $cart->getRestaurant(),
            'items' => $cart->getItems(),
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'discountAmount' => $discount,
            'total' => $total,
            'addresses' => $addresses,
        ])->layout('layouts.app', ['title' => 'إتمام الطلب']);
    }
}
