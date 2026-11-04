<div>
    {{-- رأس الصفحة --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 mb-6 border-b border-sufra-border">
        <div>
            <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">طلباتي</h1>
            <p class="text-14 text-sufra-muted mt-1">استعراض جميع طلباتك السابقة ومتابعة مسار طلباتك الجارية لحظياً.</p>
        </div>

        {{-- أزرار التصفية --}}
        <div class="flex items-center gap-2">
            <button
                type="button"
                wire:click="$set('filter', 'all')"
                class="px-3.5 py-1.5 rounded text-14 font-medium border transition-colors {{ $filter === 'all' ? 'bg-sufra-primary text-sufra-surface border-transparent' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
            >
                الكل
            </button>
            <button
                type="button"
                wire:click="$set('filter', 'active')"
                class="px-3.5 py-1.5 rounded text-14 font-medium border transition-colors {{ $filter === 'active' ? 'bg-sufra-primary text-sufra-surface border-transparent' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
            >
                طلبات جارية
            </button>
            <button
                type="button"
                wire:click="$set('filter', 'completed')"
                class="px-3.5 py-1.5 rounded text-14 font-medium border transition-colors {{ $filter === 'completed' ? 'bg-sufra-primary text-sufra-surface border-transparent' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
            >
                مكتملة ومنتهية
            </button>
        </div>
    </div>

    {{-- رسائل الإشعار أو الخطأ --}}
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

    @if ($errors->has('order_action'))
        <div class="mb-6 p-4 rounded border border-sufra-accent bg-sufra-surface text-14 text-sufra-accent flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-sufra-accent flex-shrink-0"></i>
            <span>{{ $errors->first('order_action') }}</span>
        </div>
    @endif

    {{-- قائمة الطلبات أو الحالة الفارغة --}}
    @if ($orders->isEmpty())
        <div class="py-8">
            <x-empty-state
                title="لا توجد طلبات في هذا القسم"
                description="لم تسجل أي طلبات هنا بعد. استكشف المطاعم المحلية الشهية وابدأ طلبك الآن."
                icon="shopping-bag"
            >
                <x-slot:action>
                    <x-button href="{{ route('restaurants.index') }}" variant="primary" icon="utensils">
                        استكشف المطاعم
                    </x-button>
                </x-slot:action>
            </x-empty-state>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <div class="bg-sufra-surface border border-sufra-border rounded p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 transition-colors hover:border-sufra-primary/40">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2.5 mb-2">
                            <span class="font-heading font-semibold text-16 text-sufra-text">
                                طلب #{{ $order->id }}
                            </span>
                            <span class="text-14 text-sufra-muted">&bull;</span>
                            <span class="font-medium text-16 text-sufra-text">
                                {{ $order->restaurant->name }}
                            </span>
                            <x-badge :variant="$order->status_badge_variant">
                                {{ $order->status_label }}
                            </x-badge>
                        </div>

                        <div class="text-14 text-sufra-muted flex flex-wrap items-center gap-y-1 gap-x-4 mb-2">
                            <span>{{ $order->created_at->format('Y/m/d - H:i') }}</span>
                            <span>&bull;</span>
                            <span>{{ $order->items->count() }} أصناف: {{ $order->items->pluck('name')->implode('، ') }}</span>
                        </div>

                        <div class="text-14">
                            <span class="text-sufra-muted">الإجمالي:</span>
                            <strong class="font-heading font-semibold text-sufra-primary ms-1 text-16">{{ $order->formatted_total }}</strong>
                            <span class="text-sufra-muted ms-1">({{ $order->payment_method === 'cash' ? 'نقدياً عند الاستلام' : 'دفع إلكتروني' }})</span>
                        </div>
                    </div>

                    {{-- أزرار الإجراءات --}}
                    <div class="flex flex-wrap items-center gap-2 pt-3 sm:pt-0 border-t sm:border-t-0 border-sufra-border flex-shrink-0">
                        {{-- زر التتبع --}}
                        <a
                            href="{{ route('orders.track', $order->id) }}"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                        >
                            <i data-lucide="navigation" class="w-4 h-4"></i>
                            <span>تتبع الطلب</span>
                        </a>

                        {{-- زر أعد الطلب (المتطلب 7) --}}
                        <button
                            type="button"
                            wire:click="reorder({{ $order->id }})"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded border border-sufra-border bg-sufra-surface hover:bg-sufra-bg text-sufra-text text-14 font-medium transition-colors"
                        >
                            <i data-lucide="rotate-ccw" class="w-4 h-4 text-sufra-primary"></i>
                            <span>أعد الطلب</span>
                        </button>

                        {{-- زر إلغاء الطلب قبل القبول فقط --}}
                        @if ($order->canBeCancelledByCustomer())
                            <button
                                type="button"
                                wire:click="cancelOrder({{ $order->id }})"
                                wire:confirm="هل أنت متأكد من رغبتك في إلغاء هذا الطلب؟"
                                class="inline-flex items-center gap-1 px-3 py-2 rounded border border-sufra-border text-sufra-accent hover:bg-sufra-bg text-14 font-medium transition-colors"
                            >
                                <i data-lucide="ban" class="w-3.5 h-3.5"></i>
                                <span>إلغاء</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>

