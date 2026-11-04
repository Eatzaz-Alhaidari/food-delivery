<div wire:poll.10s="pollOrders">
    {{-- تنبيه خفيف هادئ عند وصول طلب جديد (المتطلب 5) --}}
    @if ($hasNewOrderAlert)
        <div class="mb-6 p-4 rounded border border-sufra-primary bg-sufra-surface text-14 text-sufra-text flex items-center justify-between shadow-none animate-pulse">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-sufra-primary/10 text-sufra-primary flex items-center justify-center flex-shrink-0">
                    <i data-lucide="bell-ring" class="w-4 h-4"></i>
                </div>
                <div>
                    <strong class="font-heading font-semibold text-14 block text-sufra-primary">وصل طلب جديد للمطعم!</strong>
                    <span class="text-14 text-sufra-muted">يوجد طلب بانتظار القبول والمراجعة الآن.</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click="$set('statusFilter', 'pending')"
                    class="px-3 py-1.5 rounded border border-sufra-primary text-14 font-medium text-sufra-primary hover:bg-sufra-primary hover:text-sufra-surface transition-colors"
                >
                    عرض الطلبات الجديدة
                </button>
                <button
                    type="button"
                    wire:click="dismissNewOrderAlert"
                    class="p-1.5 text-sufra-muted hover:text-sufra-text"
                >
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- رسالة تأكيد العمليات --}}
    @if ($feedbackMessage)
        <div class="mb-6 p-4 rounded border border-sufra-border bg-sufra-surface text-14 text-sufra-primary flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-sufra-primary"></i>
                <span>{{ $feedbackMessage }}</span>
            </div>
            <button type="button" wire:click="$set('feedbackMessage', null)" class="text-sufra-muted hover:text-sufra-text">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    {{-- تنبيه أي خطأ في الانتقال --}}
    @if ($errors->has('order_action'))
        <div class="mb-6 p-4 rounded border border-sufra-accent bg-sufra-surface text-14 text-sufra-accent flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-sufra-accent flex-shrink-0"></i>
            <span>{{ $errors->first('order_action') }}</span>
        </div>
    @endif

    {{-- رأس الصفحة وتبويبات الفلترة --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-5 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-sufra-border">
            <div>
                <h1 class="font-heading font-semibold text-20 text-sufra-text">الطلبات الواردة</h1>
                <p class="text-14 text-sufra-muted mt-1">متابعة الطلبات وتحديث حالات التجهيز والتسليم لحظياً.</p>
            </div>
            <div class="flex items-center gap-2 text-14 text-sufra-muted">
                <span class="inline-block w-2 h-2 rounded-full bg-sufra-primary animate-ping"></span>
                <span>تحديث حي كل 10 ثوانٍ</span>
            </div>
        </div>

        {{-- أزرار تصفية الحالات --}}
        <div class="flex flex-wrap items-center gap-2 pt-4">
            <button
                type="button"
                wire:click="$set('statusFilter', 'all')"
                class="px-3.5 py-1.5 rounded text-14 font-medium border transition-colors {{ $statusFilter === 'all' ? 'bg-sufra-primary text-sufra-surface border-transparent' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
            >
                جميع الطلبات
            </button>

            <button
                type="button"
                wire:click="$set('statusFilter', 'pending')"
                class="px-3.5 py-1.5 rounded text-14 font-medium border transition-colors flex items-center gap-1.5 {{ $statusFilter === 'pending' ? 'bg-sufra-primary text-sufra-surface border-transparent' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
            >
                <span>طلبات جديدة (قيد الانتظار)</span>
                @if ($pendingCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-14 font-semibold {{ $statusFilter === 'pending' ? 'bg-sufra-surface text-sufra-primary' : 'bg-sufra-accent text-sufra-surface' }}">
                        {{ $pendingCount }}
                    </span>
                @endif
            </button>

            <button
                type="button"
                wire:click="$set('statusFilter', 'active')"
                class="px-3.5 py-1.5 rounded text-14 font-medium border transition-colors flex items-center gap-1.5 {{ $statusFilter === 'active' ? 'bg-sufra-primary text-sufra-surface border-transparent' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
            >
                <span>طلبات جارية ومقبولة</span>
                @if ($activeCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-14 font-semibold {{ $statusFilter === 'active' ? 'bg-sufra-surface text-sufra-primary' : 'bg-sufra-primary text-sufra-surface' }}">
                        {{ $activeCount }}
                    </span>
                @endif
            </button>

            <button
                type="button"
                wire:click="$set('statusFilter', 'completed')"
                class="px-3.5 py-1.5 rounded text-14 font-medium border transition-colors {{ $statusFilter === 'completed' ? 'bg-sufra-primary text-sufra-surface border-transparent' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
            >
                مكتملة ومنتهية
            </button>
        </div>
    </div>

    {{-- قائمة الطلبات --}}
    @if ($orders->isEmpty())
        <div class="py-8">
            <x-empty-state
                title="لا توجد طلبات مطابقة"
                description="لم يتم استلام أي طلبات في هذا القسم بعد."
                icon="shopping-bag"
            />
        </div>
    @else
        <div class="space-y-3">
            @foreach ($orders as $order)
                <div class="bg-sufra-surface border border-sufra-border rounded p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 transition-colors hover:border-sufra-primary/40">
                    {{-- البيانات الأساسية للطلب --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="font-heading font-semibold text-16 text-sufra-text">
                                طلب #{{ $order->id }}
                            </span>
                            <x-badge :variant="$order->status_badge_variant">
                                {{ $order->status_label }}
                            </x-badge>
                            <span class="text-14 text-sufra-muted border-s border-sufra-border ps-2">
                                {{ $order->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-14">
                            <div>
                                <span class="text-sufra-muted">العميل:</span>
                                <span class="font-medium text-sufra-text ms-1">{{ $order->user->name }}</span>
                            </div>
                            <div>
                                <span class="text-sufra-muted">التوصيل:</span>
                                <span class="font-medium text-sufra-text ms-1">{{ $order->address?->district ?? 'حي غير محدد' }}</span>
                            </div>
                            <div>
                                <span class="text-sufra-muted">المبلغ:</span>
                                <strong class="font-heading font-semibold text-sufra-primary ms-1">{{ $order->formatted_total }}</strong>
                                <span class="text-14 text-sufra-muted">({{ $order->payment_method === 'cash' ? 'كاش' : 'إلكتروني' }})</span>
                            </div>
                        </div>

                        {{-- ملخص الأصناف --}}
                        <div class="mt-2 text-14 text-sufra-muted truncate">
                            <span class="text-sufra-text font-medium">{{ $order->items->count() }} أصناف:</span>
                            {{ $order->items->pluck('name')->implode('، ') }}
                        </div>

                        @if ($order->notes)
                            <div class="mt-1 text-14 text-sufra-muted italic">
                                ملاحظة: "{{ $order->notes }}"
                            </div>
                        @endif
                    </div>

                    {{-- أزرار اتخاذ القرار وتحديث الحالة التالية فقط بحسب المتطلب 4 --}}
                    <div class="flex flex-wrap items-center gap-2 pt-3 lg:pt-0 border-t lg:border-t-0 border-sufra-border flex-shrink-0">
                        {{-- في حالة قيد الانتظار: قبول أو رفض --}}
                        @if ($order->status === \App\Enums\OrderStatus::Pending)
                            <button
                                type="button"
                                wire:click="advanceStatus({{ $order->id }})"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                            >
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span>قبول الطلب</span>
                            </button>
                            <button
                                type="button"
                                wire:click="openRejectModal({{ $order->id }})"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded border border-sufra-border text-sufra-accent hover:bg-sufra-bg text-14 font-medium transition-colors"
                            >
                                <i data-lucide="x" class="w-4 h-4"></i>
                                <span>رفض</span>
                            </button>
                        @elseif ($order->status === \App\Enums\OrderStatus::Accepted)
                            <button
                                type="button"
                                wire:click="advanceStatus({{ $order->id }})"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                            >
                                <i data-lucide="utensils" class="w-4 h-4"></i>
                                <span>بدء التجهيز</span>
                            </button>
                        @elseif ($order->status === \App\Enums\OrderStatus::Preparing)
                            <button
                                type="button"
                                wire:click="advanceStatus({{ $order->id }})"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                            >
                                <i data-lucide="package-check" class="w-4 h-4"></i>
                                <span>جاهز للتوصيل</span>
                            </button>
                        @elseif ($order->status === \App\Enums\OrderStatus::Ready)
                            <button
                                type="button"
                                wire:click="advanceStatus({{ $order->id }})"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                            >
                                <i data-lucide="truck" class="w-4 h-4"></i>
                                <span>تسليم للسائق (في الطريق)</span>
                            </button>
                        @elseif ($order->status === \App\Enums\OrderStatus::OutForDelivery)
                            <button
                                type="button"
                                wire:click="advanceStatus({{ $order->id }})"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                            >
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                <span>تأكيد التسليم للعميل</span>
                            </button>
                        @endif

                        {{-- زر عرض تفاصيل الطلب الكاملة --}}
                        <button
                            type="button"
                            wire:click="showOrderDetails({{ $order->id }})"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded border border-sufra-border text-14 font-medium text-sufra-text hover:bg-sufra-bg transition-colors"
                        >
                            <i data-lucide="eye" class="w-4 h-4 text-sufra-muted"></i>
                            <span>التفاصيل</span>
                        </button>
                    </div>
                </div>
            @endforeach

            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        </div>
    @endif

    {{-- نافذة تفاصيل الطلب وسجل الحركات --}}
    @if ($selectedOrder)
        <div
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="order-details-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-sufra-text/40 transition-opacity" wire:click="closeOrderDetails"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl bg-sufra-surface border border-sufra-border rounded shadow-none overflow-hidden my-8">
                    {{-- رأس النافذة --}}
                    <div class="p-4 sm:p-5 border-b border-sufra-border flex items-center justify-between bg-sufra-surface">
                        <div class="flex items-center gap-2">
                            <h3 id="order-details-title" class="font-heading font-semibold text-16 text-sufra-text">
                                تفاصيل الطلب #{{ $selectedOrder->id }}
                            </h3>
                            <x-badge :variant="$selectedOrder->status_badge_variant">
                                {{ $selectedOrder->status_label }}
                            </x-badge>
                        </div>
                        <button
                            type="button"
                            wire:click="closeOrderDetails"
                            class="p-1 rounded text-sufra-muted hover:text-sufra-text"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    {{-- جسم النافذة --}}
                    <div class="p-4 sm:p-5 space-y-5 max-h-[75vh] overflow-y-auto">
                        {{-- معلومات العميل والتوصيل --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-14 bg-sufra-bg p-3.5 rounded border border-sufra-border">
                            <div>
                                <span class="text-sufra-muted block">العميل:</span>
                                <strong class="text-sufra-text">{{ $selectedOrder->user->name }}</strong>
                                <span class="text-sufra-muted block text-14 mt-0.5">{{ $selectedOrder->user->email }}</span>
                            </div>
                            <div>
                                <span class="text-sufra-muted block">عنوان التوصيل:</span>
                                @if ($selectedOrder->address)
                                    <strong class="text-sufra-text">{{ $selectedOrder->address->title }} - {{ $selectedOrder->address->district }}</strong>
                                    <span class="text-sufra-muted block text-14 mt-0.5">
                                        {{ $selectedOrder->address->street }}
                                        @if ($selectedOrder->address->details)
                                            &bull; {{ $selectedOrder->address->details }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-sufra-muted">غير محدد</span>
                                @endif
                            </div>
                        </div>

                        {{-- الأصناف المطلوبة --}}
                        <div>
                            <h4 class="font-heading font-medium text-16 text-sufra-text mb-2 border-b border-sufra-border pb-1">
                                الأصناف المطلوبة
                            </h4>
                            <div class="divide-y divide-sufra-border/60 text-14">
                                @foreach ($selectedOrder->items as $item)
                                    <div class="py-2.5 flex items-start justify-between gap-2">
                                        <div>
                                            <span class="font-medium text-sufra-text">
                                                {{ $item->quantity }} &times; {{ $item->name }}
                                            </span>
                                            @if (! empty($item->addons_list))
                                                <div class="text-14 text-sufra-muted mt-0.5">
                                                    إضافات: {{ collect($item->addons_list)->pluck('name')->implode('، ') }}
                                                </div>
                                            @endif
                                        </div>
                                        <span class="font-semibold text-sufra-text">
                                            {{ $item->formatted_line_total }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-3 border-t border-sufra-border space-y-1 text-14 text-sufra-muted">
                                <div class="flex justify-between">
                                    <span>المجموع الفرعي:</span>
                                    <span class="text-sufra-text">{{ $selectedOrder->formatted_subtotal }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>رسوم التوصيل:</span>
                                    <span class="text-sufra-text">{{ $selectedOrder->formatted_delivery_fee }}</span>
                                </div>
                                <div class="flex justify-between font-heading font-semibold text-16 text-sufra-text pt-1 border-t border-sufra-border">
                                    <span>الإجمالي:</span>
                                    <span class="text-sufra-primary">{{ $selectedOrder->formatted_total }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- سجل حركات تغير الحالة (المتطلب 3) --}}
                        <div>
                            <h4 class="font-heading font-medium text-16 text-sufra-text mb-2 border-b border-sufra-border pb-1">
                                سجل تغيرات الحالات (Order Status Logs)
                            </h4>
                            <div class="space-y-2 text-14">
                                @foreach ($selectedOrder->statusLogs as $log)
                                    <div class="p-2.5 rounded border border-sufra-border bg-sufra-bg/40 flex items-start justify-between gap-2">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-sufra-text">
                                                    {{ $log->to_status?->label() ?? 'الحالة' }}
                                                </span>
                                                <span class="text-14 text-sufra-muted">
                                                    بواسطة: {{ $log->changed_by_name }}
                                                </span>
                                            </div>
                                            @if ($log->comment)
                                                <p class="text-14 text-sufra-muted mt-1 italic">
                                                    "{{ $log->comment }}"
                                                </p>
                                            @endif
                                        </div>
                                        <span class="text-14 text-sufra-muted flex-shrink-0">
                                            {{ $log->created_at->format('H:i - Y/m/d') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ذيل النافذة --}}
                    <div class="p-4 border-t border-sufra-border bg-sufra-surface flex justify-end">
                        <button
                            type="button"
                            wire:click="closeOrderDetails"
                            class="px-4 py-2 rounded border border-sufra-border text-14 font-medium text-sufra-text hover:bg-sufra-bg transition-colors"
                        >
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- نافذة رفض الطلب مع اشتراط كتابة السبب (المتطلب 4) --}}
    @if ($showRejectModal)
        <div
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="reject-modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-sufra-text/40 transition-opacity" wire:click="closeRejectModal"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-sufra-surface border border-sufra-border rounded shadow-none p-5 sm:p-6 my-8 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-sufra-border">
                        <div class="flex items-center gap-2 text-sufra-accent">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            <h3 id="reject-modal-title" class="font-heading font-semibold text-16 text-sufra-text">
                                رفض الطلب #{{ $rejectOrderId }}
                            </h3>
                        </div>
                        <button
                            type="button"
                            wire:click="closeRejectModal"
                            class="p-1 rounded text-sufra-muted hover:text-sufra-text"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="submitReject" class="space-y-4">
                        <div>
                            <label for="reject-reason" class="block font-heading font-medium text-14 text-sufra-text mb-1">
                                سبب الرفض (إلزامي):
                            </label>
                            <textarea
                                id="reject-reason"
                                wire:model.defer="rejectReason"
                                rows="3"
                                placeholder="مثال: نفاد مكونات الطبق، ضغط تشغيلي في المطبخ، خارج نطاق التوصيل..."
                                class="w-full rounded border border-sufra-border bg-sufra-surface px-3 py-2 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:ring-0 transition-colors resize-none"
                            ></textarea>
                            <x-input-error :messages="$errors->get('rejectReason')" class="mt-1" />
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-sufra-border">
                            <button
                                type="button"
                                wire:click="closeRejectModal"
                                class="px-4 py-2 rounded border border-sufra-border text-14 font-medium text-sufra-text bg-sufra-surface hover:bg-sufra-bg transition-colors"
                            >
                                تراجع
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 rounded border border-transparent bg-sufra-accent text-sufra-surface text-14 font-medium hover:bg-sufra-accent/90 transition-colors shadow-none"
                            >
                                تأكيد الرفض
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
