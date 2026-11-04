<div class="max-w-3xl mx-auto space-y-6">
    {{-- بطاقة التهنئة وتأكيد الاستلام --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-6 sm:p-8 text-center space-y-3">
        <div class="w-16 h-16 rounded-full bg-sufra-bg border border-sufra-border flex items-center justify-center mx-auto text-sufra-primary">
            <i data-lucide="check-circle-2" class="w-8 h-8"></i>
        </div>

        <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">
            تم استلام طلبك بنجاح!
        </h1>

        <p class="text-14 sm:text-16 text-sufra-muted max-w-md mx-auto leading-relaxed">
            شكراً لطلبك عبر سُفرة. تم إرسال تفاصيل طلبك إلى المطعم، وبدأ العمل على تجهيزه الآن.
        </p>

        <div class="pt-3 flex flex-wrap items-center justify-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-sufra-border bg-sufra-bg text-14 font-medium text-sufra-text">
                <span>رقم الطلب:</span>
                <strong class="font-heading font-semibold text-sufra-primary">#{{ $order->id }}</strong>
            </span>

            <x-badge :variant="$order->status_badge_variant">
                {{ $order->status_label }}
            </x-badge>

            <span class="text-14 text-sufra-muted">
                {{ $order->created_at->format('Y/m/d - H:i') }}
            </span>
        </div>
    </div>

    {{-- بطاقة تفاصيل الطلب والمطعم والعنوان --}}
    <div class="bg-sufra-surface border border-sufra-border rounded overflow-hidden">
        {{-- رأس البطاقة: بيانات المطعم والتوصيل --}}
        <div class="p-5 sm:p-6 border-b border-sufra-border grid grid-cols-1 sm:grid-cols-2 gap-4 text-14">
            <div>
                <span class="text-sufra-muted block">المطعم:</span>
                <span class="font-heading font-semibold text-16 text-sufra-text mt-0.5 block">
                    {{ $order->restaurant->name }}
                </span>
                <span class="text-sufra-muted flex items-center gap-1 mt-1">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-sufra-primary"></i>
                    <span>{{ $order->restaurant->district }}</span>
                    &bull;
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-sufra-primary ms-1"></i>
                    <span>تجهيز: {{ $order->restaurant->formatted_prep_time }}</span>
                </span>
            </div>

            <div>
                <span class="text-sufra-muted block">عنوان التوصيل:</span>
                @if ($order->address)
                    <span class="font-heading font-semibold text-16 text-sufra-text mt-0.5 block">
                        {{ $order->address->title }} ({{ $order->address->district }})
                    </span>
                    <span class="text-sufra-muted block mt-1">
                        {{ $order->address->street }}
                        @if ($order->address->details)
                            &bull; {{ $order->address->details }}
                        @endif
                    </span>
                @else
                    <span class="text-sufra-muted mt-0.5 block">العنوان غير محدد</span>
                @endif
            </div>
        </div>

        {{-- تفاصيل الأصناف المحفوظة باللقطة التاريخية (Snapshot) --}}
        <div class="p-5 sm:p-6 space-y-4">
            <h2 class="font-heading font-semibold text-16 text-sufra-text border-b border-sufra-border pb-2">
                الأصناف المطلوبة
            </h2>

            <div class="divide-y divide-sufra-border/60">
                @foreach ($order->items as $item)
                    <div class="py-3 first:pt-0 last:pb-0 flex items-start justify-between gap-4 text-14">
                        <div class="min-w-0">
                            <span class="font-medium text-sufra-text block">
                                {{ $item->quantity }} &times; {{ $item->name }}
                            </span>

                            @if (! empty($item->addons_list))
                                <div class="text-14 text-sufra-muted mt-1 space-y-0.5">
                                    @foreach ($item->addons_list as $addon)
                                        <div class="flex items-center gap-1">
                                            <span>+ {{ $addon['name'] }}</span>
                                            <span class="text-sufra-muted/80">({{ number_format($addon['price'], 2) }} ر.س)</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <span class="font-semibold text-sufra-text flex-shrink-0">
                            {{ $item->formatted_line_total }}
                        </span>
                    </div>
                @endforeach
            </div>

            @if ($order->notes)
                <div class="pt-3 border-t border-sufra-border text-14">
                    <span class="font-medium text-sufra-text block">ملاحظات الطلب:</span>
                    <p class="text-sufra-muted italic mt-0.5">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        {{-- ملخص الحساب النهائي وطريقة الدفع --}}
        <div class="p-5 sm:p-6 bg-sufra-bg border-t border-sufra-border space-y-2 text-14">
            <div class="flex items-center justify-between text-sufra-muted">
                <span>المجموع الفرعي</span>
                <span class="font-medium text-sufra-text">{{ $order->formatted_subtotal }}</span>
            </div>
            <div class="flex items-center justify-between text-sufra-muted">
                <span>رسوم التوصيل</span>
                <span class="font-medium text-sufra-text">{{ $order->formatted_delivery_fee }}</span>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-sufra-border font-heading font-semibold text-16 text-sufra-text">
                <span>الإجمالي المدفوع</span>
                <span class="text-20 text-sufra-primary">{{ $order->formatted_total }}</span>
            </div>
            <div class="pt-2 flex items-center justify-between text-14 text-sufra-muted border-t border-sufra-border/60">
                <span>طريقة الدفع:</span>
                <span class="font-medium text-sufra-text">{{ $order->payment_method_label }}</span>
            </div>
        </div>
    </div>

    {{-- أزرار التنقل السريع --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
        <x-button href="{{ route('orders.track', $order->id) }}" variant="primary" icon="navigation">
            تتبع مسار طلبك الآن
        </x-button>
        <x-button href="{{ route('orders.index') }}" variant="secondary">
            قائمة طلباتي
        </x-button>
        <x-button href="{{ route('restaurants.index') }}" variant="secondary">
            تصفح مطاعم أخرى
        </x-button>
    </div>
</div>

