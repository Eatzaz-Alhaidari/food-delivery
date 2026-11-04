<div class="space-y-6">
    {{-- رأس الصفحة --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-sufra-border">
        <div>
            <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">كافة الطلبات</h1>
            <p class="text-14 text-sufra-muted mt-1">سجل تفصيلي لجميع طلبات المنصة مع فلترة متقدمة واستعراض سجل الحركات.</p>
        </div>
    </div>

    {{-- شريط الفلاتر المتقدمة (المتطلب 4: الحالة، المطعم، التاريخ) --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-4 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- البحث --}}
            <div>
                <label for="filter-search" class="block text-14 text-sufra-muted mb-1">البحث:</label>
                <input
                    id="filter-search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="رقم الطلب، العميل، المطعم..."
                    class="w-full rounded border border-sufra-border bg-sufra-bg px-3 py-1.5 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:bg-sufra-surface focus:ring-0 transition-colors"
                >
            </div>

            {{-- فلترة بالحالة --}}
            <div>
                <label for="filter-status" class="block text-14 text-sufra-muted mb-1">الحالة:</label>
                <select
                    id="filter-status"
                    wire:model.live="statusFilter"
                    class="w-full rounded border border-sufra-border bg-sufra-bg px-3 py-1.5 text-14 text-sufra-text focus:border-sufra-primary focus:bg-sufra-surface focus:ring-0 transition-colors"
                >
                    <option value="all">جميع الحالات</option>
                    @foreach (\App\Enums\OrderStatus::cases() as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            {{-- فلترة بالمطعم --}}
            <div>
                <label for="filter-restaurant" class="block text-14 text-sufra-muted mb-1">المطعم:</label>
                <select
                    id="filter-restaurant"
                    wire:model.live="restaurantFilter"
                    class="w-full rounded border border-sufra-border bg-sufra-bg px-3 py-1.5 text-14 text-sufra-text focus:border-sufra-primary focus:bg-sufra-surface focus:ring-0 transition-colors"
                >
                    <option value="all">جميع المطاعم</option>
                    @foreach ($restaurants as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- فلترة بالتاريخ --}}
            <div>
                <label for="filter-date" class="block text-14 text-sufra-muted mb-1">التاريخ:</label>
                <select
                    id="filter-date"
                    wire:model.live="dateFilter"
                    class="w-full rounded border border-sufra-border bg-sufra-bg px-3 py-1.5 text-14 text-sufra-text focus:border-sufra-primary focus:bg-sufra-surface focus:ring-0 transition-colors"
                >
                    <option value="all">كافة الأوقات</option>
                    <option value="today">طلبات اليوم</option>
                    <option value="week">آخر 7 أيام</option>
                    <option value="month">آخر 30 يوماً</option>
                </select>
            </div>
        </div>

        @if ($search !== '' || $statusFilter !== 'all' || $restaurantFilter !== 'all' || $dateFilter !== 'all')
            <div class="pt-2 border-t border-sufra-border/60 flex items-center justify-between text-14">
                <span class="text-sufra-muted">يتم تطبيق فلاتر مخصصة على النتائج</span>
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="text-sufra-primary hover:underline font-medium"
                >
                    إعادة ضبط الفلاتر
                </button>
            </div>
        @endif
    </div>

    {{-- جدول الطلبات --}}
    @if ($orders->isEmpty())
        <div class="py-8">
            <x-empty-state
                title="لا توجد طلبات مطابقة"
                description="لم يتم العثور على أي طلبات تطابق الفلاتر المحددة."
                icon="shopping-bag"
            />
        </div>
    @else
        <div class="bg-sufra-surface border border-sufra-border rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-start text-14">
                    <thead class="bg-sufra-bg border-b border-sufra-border text-sufra-muted font-medium">
                        <tr>
                            <th class="p-3.5 text-start">رقم الطلب</th>
                            <th class="p-3.5 text-start">المطعم</th>
                            <th class="p-3.5 text-start">العميل</th>
                            <th class="p-3.5 text-start">التاريخ والوقت</th>
                            <th class="p-3.5 text-start">المبلغ الإجمالي</th>
                            <th class="p-3.5 text-start">الحالة</th>
                            <th class="p-3.5 text-end">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sufra-border/60">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-sufra-bg/40 transition-colors">
                                <td class="p-3.5 font-heading font-semibold text-sufra-text">
                                    #{{ $order->id }}
                                </td>
                                <td class="p-3.5 font-medium text-sufra-text">
                                    {{ $order->restaurant->name }}
                                </td>
                                <td class="p-3.5">
                                    <span class="text-sufra-text block font-medium">{{ $order->user->name }}</span>
                                    <span class="text-14 text-sufra-muted block">{{ $order->address?->district ?? 'حي غير محدد' }}</span>
                                </td>
                                <td class="p-3.5 text-sufra-muted">
                                    <span class="block text-sufra-text">{{ $order->created_at->format('Y/m/d') }}</span>
                                    <span class="block text-14">{{ $order->created_at->format('H:i') }}</span>
                                </td>
                                <td class="p-3.5">
                                    <strong class="font-heading font-semibold text-sufra-primary block">
                                        {{ $order->formatted_total }}
                                    </strong>
                                    <span class="text-14 text-sufra-muted">
                                        {{ $order->payment_method === 'cash' ? 'كاش' : 'إلكتروني' }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    <x-badge :variant="$order->status_badge_variant">
                                        {{ $order->status_label }}
                                    </x-badge>
                                </td>
                                <td class="p-3.5 text-end">
                                    <button
                                        type="button"
                                        wire:click="showDetails({{ $order->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded border border-sufra-border text-14 font-medium text-sufra-text hover:bg-sufra-bg transition-colors"
                                    >
                                        <i data-lucide="eye" class="w-3.5 h-3.5 text-sufra-muted"></i>
                                        <span>معاينة</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-sufra-border">
                {{ $orders->links() }}
            </div>
        </div>
    @endif

    {{-- نافذة استعراض تفاصيل الطلب بالكامل وسجل الحركات --}}
    @if ($selectedOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-sufra-text/40 transition-opacity" wire:click="closeDetails"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl bg-sufra-surface border border-sufra-border rounded shadow-none p-5 sm:p-6 my-8 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-sufra-border">
                        <div class="flex items-center gap-2">
                            <h3 class="font-heading font-semibold text-16 text-sufra-text">
                                تفاصيل الطلب #{{ $selectedOrder->id }}
                            </h3>
                            <x-badge :variant="$selectedOrder->status_badge_variant">
                                {{ $selectedOrder->status_label }}
                            </x-badge>
                        </div>
                        <button type="button" wire:click="closeDetails" class="p-1 rounded text-sufra-muted hover:text-sufra-text">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    {{-- تفاصيل المطعم والعميل --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-14 bg-sufra-bg p-3.5 rounded border border-sufra-border">
                        <div>
                            <span class="text-sufra-muted block">المطعم:</span>
                            <strong class="text-sufra-text block font-medium">{{ $selectedOrder->restaurant->name }}</strong>
                            <span class="text-sufra-muted text-14 block">{{ $selectedOrder->restaurant->district }}</span>
                        </div>
                        <div>
                            <span class="text-sufra-muted block">العميل والتوصيل:</span>
                            <strong class="text-sufra-text block font-medium">{{ $selectedOrder->user->name }}</strong>
                            <span class="text-sufra-muted text-14 block">
                                {{ $selectedOrder->address?->district ?? 'حي غير محدد' }} - {{ $selectedOrder->address?->street }}
                            </span>
                        </div>
                    </div>

                    {{-- الأصناف بأسعار اللقطة --}}
                    <div>
                        <h4 class="font-heading font-medium text-14 text-sufra-text border-b border-sufra-border pb-1 mb-2">
                            الأصناف في الطلب
                        </h4>
                        <div class="divide-y divide-sufra-border/60 text-14">
                            @foreach ($selectedOrder->items as $item)
                                <div class="py-2 flex items-start justify-between gap-2">
                                    <div>
                                        <span class="font-medium text-sufra-text">{{ $item->quantity }} &times; {{ $item->name }}</span>
                                        @if (! empty($item->addons_list))
                                            <div class="text-14 text-sufra-muted">
                                                إضافات: {{ collect($item->addons_list)->pluck('name')->implode('، ') }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="font-semibold text-sufra-text">{{ $item->formatted_line_total }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-2 border-t border-sufra-border text-14 space-y-1">
                            <div class="flex justify-between text-sufra-muted">
                                <span>المجموع الفرعي:</span>
                                <span>{{ $selectedOrder->formatted_subtotal }}</span>
                            </div>
                            <div class="flex justify-between text-sufra-muted">
                                <span>رسوم التوصيل:</span>
                                <span>{{ $selectedOrder->formatted_delivery_fee }}</span>
                            </div>
                            <div class="flex justify-between font-heading font-semibold text-16 text-sufra-text pt-1 border-t border-sufra-border">
                                <span>الإجمالي:</span>
                                <span class="text-sufra-primary">{{ $selectedOrder->formatted_total }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- سجل حركة تغير الحالة (Order Status Logs) --}}
                    <div>
                        <h4 class="font-heading font-medium text-14 text-sufra-text border-b border-sufra-border pb-1 mb-2">
                            سجل الحركات (Status Logs)
                        </h4>
                        <div class="space-y-1.5 text-14 max-h-40 overflow-y-auto">
                            @foreach ($selectedOrder->statusLogs as $log)
                                <div class="p-2 rounded bg-sufra-bg border border-sufra-border/70 flex items-start justify-between gap-2">
                                    <div>
                                        <strong class="text-sufra-text">{{ $log->to_status?->label() }}</strong>
                                        <span class="text-sufra-muted">({{ $log->changed_by_name }})</span>
                                        @if ($log->comment)
                                            <p class="text-sufra-muted italic mt-0.5">"{{ $log->comment }}"</p>
                                        @endif
                                    </div>
                                    <span class="text-14 text-sufra-muted flex-shrink-0">{{ $log->created_at->format('H:i Y/m/d') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end pt-2 border-t border-sufra-border">
                        <button type="button" wire:click="closeDetails" class="px-4 py-2 rounded border border-sufra-border text-14 font-medium text-sufra-text hover:bg-sufra-bg transition-colors">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

