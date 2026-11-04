<div class="space-y-6">
    {{-- رأس الصفحة --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-sufra-border">
        <div>
            <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">تقرير المبيعات والعمولة</h1>
            <p class="text-14 text-sufra-muted mt-1">تفصيل المبيعات المحققة للطلبات المسلّمة واحتساب عمولة المنصة لكل مطبخ ومطعم.</p>
        </div>
        <a
            href="{{ route('admin.settings') }}"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded border border-sufra-border bg-sufra-surface hover:bg-sufra-bg text-14 font-medium text-sufra-text transition-colors"
        >
            <i data-lucide="settings" class="w-4 h-4 text-sufra-primary"></i>
            <span>تعديل نسبة العمولة ({{ $commissionRate }}%)</span>
        </a>
    </div>

    {{-- بطاقات الملخص الإجمالي المالي --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- إجمالي مبيعات المطاعم --}}
        <div class="bg-sufra-surface border border-sufra-border rounded p-4">
            <span class="text-14 text-sufra-muted block">إجمالي مبيعات المنصة (المسلّمة)</span>
            <strong class="font-heading font-semibold text-28 text-sufra-text block mt-1">
                {{ number_format($totalSalesAll, 2) }}
                <span class="text-16 font-sans font-normal text-sufra-muted">ر.س</span>
            </strong>
            <span class="text-14 text-sufra-muted mt-0.5 block">من كافة المطاعم المسجلة</span>
        </div>

        {{-- إجمالي عمولة المنصة --}}
        <div class="bg-sufra-surface border border-sufra-border rounded p-4">
            <span class="text-14 text-sufra-muted block">عمولة المنصة المحصلة ({{ $commissionRate }}%)</span>
            <strong class="font-heading font-semibold text-28 text-sufra-primary block mt-1">
                {{ number_format($totalCommissionAll, 2) }}
                <span class="text-16 font-sans font-normal text-sufra-muted">ر.س</span>
            </strong>
            <span class="text-14 text-sufra-muted mt-0.5 block">صافي إيرادات منصة سُفرة</span>
        </div>

        {{-- صافي مستحقات المطاعم --}}
        <div class="bg-sufra-surface border border-sufra-border rounded p-4">
            <span class="text-14 text-sufra-muted block">صافي مستحقات المطاعم ({{ 100 - $commissionRate }}%)</span>
            <strong class="font-heading font-semibold text-28 text-sufra-text block mt-1">
                {{ number_format($totalNetPayoutAll, 2) }}
                <span class="text-16 font-sans font-normal text-sufra-muted">ر.س</span>
            </strong>
            <span class="text-14 text-sufra-muted mt-0.5 block">المبالغ المستحقة للتحويل للملاك</span>
        </div>
    </div>

    {{-- جدول تفصيل المبيعات والعمولة لكل مطعم (المتطلب 6) --}}
    <div class="bg-sufra-surface border border-sufra-border rounded overflow-hidden">
        <div class="p-4 bg-sufra-bg/40 border-b border-sufra-border flex items-center justify-between">
            <h2 class="font-heading font-semibold text-16 text-sufra-text">تقرير تفصيلي بمبيعات وعمولات كل مطعم</h2>
            <span class="text-14 text-sufra-muted">{{ $restaurants->count() }} مطاعم مسجلة</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-14">
                <thead class="bg-sufra-bg border-b border-sufra-border text-sufra-muted font-medium">
                    <tr>
                        <th class="p-3.5 text-start">المطعم</th>
                        <th class="p-3.5 text-start">الحي والتصنيف</th>
                        <th class="p-3.5 text-start">الطلبات المسلّمة</th>
                        <th class="p-3.5 text-start">إجمالي المبيعات</th>
                        <th class="p-3.5 text-start">عمولة المنصة ({{ $commissionRate }}%)</th>
                        <th class="p-3.5 text-start">صافي المستحق للمطعم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sufra-border/60">
                    @forelse ($restaurants as $restaurant)
                        @php
                            $sales = (float) ($restaurant->total_sales ?? 0);
                        @endphp
                        <tr class="hover:bg-sufra-bg/40 transition-colors">
                            <td class="p-3.5">
                                <strong class="font-heading font-semibold text-16 text-sufra-text block">
                                    {{ $restaurant->name }}
                                </strong>
                                <span class="text-14 text-sufra-muted">{{ $restaurant->user->name }}</span>
                            </td>
                            <td class="p-3.5 text-sufra-muted">
                                <span>{{ $restaurant->district }}</span>
                                <span class="ms-1">({{ $restaurant->cuisine }})</span>
                            </td>
                            <td class="p-3.5">
                                <span class="font-medium text-sufra-text">{{ $restaurant->delivered_orders_count }} طلب</span>
                            </td>
                            <td class="p-3.5">
                                <strong class="font-heading font-semibold text-sufra-text">
                                    {{ number_format($sales, 2) }} ر.س
                                </strong>
                            </td>
                            <td class="p-3.5">
                                <strong class="font-heading font-semibold text-sufra-primary">
                                    {{ number_format($restaurant->calculated_commission, 2) }} ر.س
                                </strong>
                            </td>
                            <td class="p-3.5">
                                <strong class="font-heading font-semibold text-sufra-text">
                                    {{ number_format($restaurant->calculated_net, 2) }} ر.س
                                </strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-sufra-muted">
                                لا توجد مطاعم مسجلة حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

