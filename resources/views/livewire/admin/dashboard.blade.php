<div class="space-y-6">
    {{-- رأس الصفحة --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-sufra-border">
        <div>
            <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">نظرة عامة على المنصة</h1>
            <p class="text-14 text-sufra-muted mt-1">متابعة فورية للمبيعات وحركة الطلبات ونشاط المطابخ المحلية.</p>
        </div>
        <div class="text-14 text-sufra-muted flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-sufra-primary animate-ping"></span>
            <span>بيانات حية مباشرة من قاعدة البيانات</span>
        </div>
    </div>

    {{-- بطاقات المؤشرات الرقمية الحقيقية (المتطلب 1) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- طلبات اليوم --}}
        <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between text-14 text-sufra-muted mb-2">
                <span>طلبات اليوم</span>
                <i data-lucide="calendar" class="w-4 h-4 text-sufra-primary"></i>
            </div>
            <div>
                <span class="font-heading font-semibold text-28 text-sufra-text block">
                    {{ $todayOrdersCount }}
                </span>
                <span class="text-14 text-sufra-muted mt-1 block">
                    مبيعات مسلّمة اليوم: <strong class="text-sufra-primary">{{ number_format($todayDeliveredSales, 2) }} ر.س</strong>
                </span>
            </div>
        </div>

        {{-- إجمالي المبيعات (للطلبات المسلمة فقط) --}}
        <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between text-14 text-sufra-muted mb-2">
                <span>المبيعات المحققة (المسلّمة)</span>
                <i data-lucide="wallet" class="w-4 h-4 text-sufra-primary"></i>
            </div>
            <div>
                <span class="font-heading font-semibold text-28 text-sufra-text block">
                    {{ number_format($totalDeliveredSales, 2) }}
                    <span class="text-16 font-sans font-normal text-sufra-muted">ر.س</span>
                </span>
                <span class="text-14 text-sufra-muted mt-1 block">
                    للطلبات المكتملة بنجاح فقط
                </span>
            </div>
        </div>

        {{-- المطاعم النشطة --}}
        <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between text-14 text-sufra-muted mb-2">
                <span>المطاعم النشطة</span>
                <i data-lucide="store" class="w-4 h-4 text-sufra-primary"></i>
            </div>
            <div>
                <span class="font-heading font-semibold text-28 text-sufra-text block">
                    {{ $activeRestaurantsCount }}
                    <span class="text-16 font-sans font-normal text-sufra-muted">/ {{ $totalRestaurantsCount }}</span>
                </span>
                <span class="text-14 text-sufra-muted mt-1 block">
                    متاحة للطلب وتظهر للعملاء
                </span>
            </div>
        </div>

        {{-- عمولة المنصة --}}
        <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between text-14 text-sufra-muted mb-2">
                <span>عمولة المنصة ({{ $commissionRate }}%)</span>
                <i data-lucide="percent" class="w-4 h-4 text-sufra-primary"></i>
            </div>
            <div>
                <span class="font-heading font-semibold text-28 text-sufra-primary block">
                    {{ number_format($estimatedCommission, 2) }}
                    <span class="text-16 font-sans font-normal text-sufra-muted">ر.س</span>
                </span>
                <span class="text-14 text-sufra-muted mt-1 block">
                    دخل المنصة المحصل من المبيعات
                </span>
            </div>
        </div>
    </div>

    {{-- الرسم البياني لحركة الطلبات في آخر 7 أيام (المتطلب 2: ألوان DESIGN.md وبدون تدرجات) --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 mb-6 border-b border-sufra-border">
            <div>
                <h2 class="font-heading font-semibold text-16 text-sufra-text">حركة الطلبات في آخر 7 أيام</h2>
                <p class="text-14 text-sufra-muted mt-0.5">عدد الطلبات المسجلة يومياً بنمط الأعمدة البيانية الهادئة.</p>
            </div>
            <div class="flex items-center gap-3 text-14 text-sufra-muted">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded bg-sufra-primary"></span>
                    <span>عدد الطلبات</span>
                </span>
            </div>
        </div>

        {{-- مخطط الأعمدة العمودية البسيطة --}}
        <div class="h-56 flex items-end justify-between gap-2 sm:gap-6 pt-4 px-2 border-b border-sufra-border">
            @foreach ($last7Days as $day)
                @php
                    $heightPercent = $maxOrders > 0 ? max(round(($day['count'] / $maxOrders) * 100), 8) : 8;
                @endphp
                <div class="flex-1 flex flex-col items-center h-full justify-end group relative">
                    {{-- بطاقة التلميح عند التحويم (Tooltip) --}}
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-150 absolute -top-8 px-2 py-1 rounded bg-sufra-text text-sufra-surface text-14 font-medium whitespace-nowrap pointer-events-none z-20">
                        {{ $day['count'] }} طلبات ({{ number_format($day['sales'], 2) }} ر.س)
                    </div>

                    {{-- عدد الطلبات أعلى العمود --}}
                    <span class="text-14 font-semibold text-sufra-text mb-1.5">
                        {{ $day['count'] }}
                    </span>

                    {{-- عمود الرسم البياني الصلب بلون زيتوني هادئ بدون تدرج نهائياً --}}
                    <div
                        class="w-full max-w-[40px] bg-sufra-primary rounded-t transition-all duration-200 group-hover:bg-sufra-primary/85"
                        style="height: {{ $heightPercent }}%;"
                    ></div>
                </div>
            @endforeach
        </div>

        {{-- مسميات الأيام أسفل الأعمدة --}}
        <div class="flex items-center justify-between gap-2 sm:gap-6 pt-2.5 px-2 text-14 text-sufra-muted font-medium">
            @foreach ($last7Days as $day)
                <div class="flex-1 text-center">
                    <span class="block truncate">{{ $day['day_label'] }}</span>
                    <span class="block text-14 text-sufra-muted/70 text-[11px]">{{ \Carbon\Carbon::parse($day['date'])->format('m/d') }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- قسم أكثر المطاعم مبيعاً وجدول آخر الطلبات في صفين متجاورين --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- قسم أكثر المطاعم مبيعاً (المتطلب 5) --}}
        <div class="lg:col-span-5 bg-sufra-surface border border-sufra-border rounded p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-sufra-border">
                <div class="flex items-center gap-2">
                    <i data-lucide="award" class="w-5 h-5 text-sufra-primary"></i>
                    <h2 class="font-heading font-semibold text-16 text-sufra-text">أكثر المطاعم مبيعاً</h2>
                </div>
                <a href="{{ route('admin.sales') }}" class="text-14 text-sufra-primary hover:underline font-medium">
                    تقرير المبيعات
                </a>
            </div>

            @if ($topRestaurants->isEmpty())
                <div class="py-6 text-center text-14 text-sufra-muted">
                    لا توجد بيانات مبيعات مكتملة بعد.
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($topRestaurants as $index => $restaurant)
                        @php
                            $sales = (float) ($restaurant->total_sales ?? 0);
                            $comm = round($sales * ($commissionRate / 100), 2);
                        @endphp
                        <div class="p-3 bg-sufra-bg/50 rounded border border-sufra-border flex items-center justify-between gap-3 text-14">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-6 h-6 rounded-full border border-sufra-border bg-sufra-surface text-14 font-semibold text-sufra-primary flex items-center justify-center flex-shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <strong class="text-sufra-text block truncate font-medium">
                                        {{ $restaurant->name }}
                                    </strong>
                                    <span class="text-sufra-muted text-14 block">
                                        {{ $restaurant->district }} &bull; {{ $restaurant->delivered_orders_count }} طلب مسلّم
                                    </span>
                                </div>
                            </div>

                            <div class="text-end flex-shrink-0">
                                <strong class="font-heading font-semibold text-sufra-text block">
                                    {{ number_format($sales, 2) }} ر.س
                                </strong>
                                <span class="text-14 text-sufra-primary block">
                                    عمولة: {{ number_format($comm, 2) }} ر.س
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- جدول آخر الطلبات (المتطلب 1) --}}
        <div class="lg:col-span-7 bg-sufra-surface border border-sufra-border rounded p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-sufra-border">
                <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-sufra-primary"></i>
                    <h2 class="font-heading font-semibold text-16 text-sufra-text">آخر الطلبات المسجلة</h2>
                </div>
                <a href="{{ route('admin.orders') }}" class="text-14 text-sufra-primary hover:underline font-medium">
                    عرض جميع الطلبات
                </a>
            </div>

            @if ($recentOrders->isEmpty())
                <div class="py-6 text-center text-14 text-sufra-muted">
                    لم يتم تسجيل أي طلبات حتى الآن.
                </div>
            @else
                <div class="divide-y divide-sufra-border/60">
                    @foreach ($recentOrders as $order)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-14">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <strong class="font-heading font-semibold text-sufra-text">
                                        طلب #{{ $order->id }}
                                    </strong>
                                    <x-badge :variant="$order->status_badge_variant">
                                        {{ $order->status_label }}
                                    </x-badge>
                                </div>
                                <div class="text-14 text-sufra-muted truncate">
                                    <span>{{ $order->restaurant->name }}</span>
                                    &bull;
                                    <span>العميل: {{ $order->user->name }}</span>
                                    &bull;
                                    <span>{{ $order->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="text-end flex-shrink-0">
                                <strong class="font-heading font-semibold text-sufra-primary block text-16">
                                    {{ $order->formatted_total }}
                                </strong>
                                <span class="text-14 text-sufra-muted">
                                    {{ $order->payment_method === 'cash' ? 'كاش' : 'إلكتروني' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

