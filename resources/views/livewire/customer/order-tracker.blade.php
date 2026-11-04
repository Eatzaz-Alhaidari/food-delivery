<div class="max-w-3xl mx-auto space-y-6" wire:poll.5s="refreshOrder">
    {{-- رسالة تأكيد أو إشعار --}}
    @if ($feedbackMessage)
        <div class="p-4 rounded border border-sufra-border bg-sufra-surface text-14 text-sufra-primary flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-sufra-primary"></i>
                <span>{{ $feedbackMessage }}</span>
            </div>
            <button type="button" wire:click="$set('feedbackMessage', null)" class="text-sufra-muted hover:text-sufra-text">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if ($errors->has('cancel') || $errors->has('reorder'))
        <div class="p-4 rounded border border-sufra-accent bg-sufra-surface text-14 text-sufra-accent flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-sufra-accent flex-shrink-0"></i>
            <span>{{ $errors->first('cancel') ?: $errors->first('reorder') }}</span>
        </div>
    @endif

    {{-- رأس صفحة التتبع --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <h1 class="font-heading font-semibold text-20 text-sufra-text">
                    تتبع الطلب #{{ $order->id }}
                </h1>
                <x-badge :variant="$order->status_badge_variant">
                    {{ $order->status_label }}
                </x-badge>
            </div>
            <p class="text-14 text-sufra-muted">
                طلبك من مطعم <strong class="text-sufra-text">{{ $order->restaurant->name }}</strong>
                &bull; {{ $order->created_at->format('Y/m/d - H:i') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            {{-- زر إلغاء الطلب (مسموح فقط قبل قبول المطعم له) --}}
            @if ($order->canBeCancelledByCustomer())
                <button
                    type="button"
                    wire:click="cancelOrder"
                    wire:confirm="هل أنت متأكد من رغبتك في إلغاء هذا الطلب؟"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded border border-sufra-border text-sufra-accent hover:bg-sufra-bg text-14 font-medium transition-colors"
                >
                    <i data-lucide="ban" class="w-4 h-4"></i>
                    <span>إلغاء الطلب</span>
                </button>
            @endif

            {{-- زر إعادة الطلب --}}
            <button
                type="button"
                wire:click="reorder"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded border border-sufra-border bg-sufra-surface hover:bg-sufra-bg text-sufra-text text-14 font-medium transition-colors"
            >
                <i data-lucide="rotate-ccw" class="w-4 h-4 text-sufra-primary"></i>
                <span>أعد الطلب</span>
            </button>
        </div>
    </div>

    {{-- شريط المراحل الهادئ (خطوط ونقاط بحسب DESIGN.md) --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-6 sm:p-8">
        @php
            $currentStep = $order->status->stepIndex();
            $isCancelled = $order->status === \App\Enums\OrderStatus::Cancelled;
            $isRejected = $order->status === \App\Enums\OrderStatus::Rejected;
        @endphp

        @if ($isRejected || $isCancelled)
            {{-- حالة جانبية (مرفوض أو ملغي) --}}
            <div class="p-5 rounded border border-sufra-accent/40 bg-sufra-bg flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-full border border-sufra-accent/30 bg-sufra-surface flex items-center justify-center text-sufra-accent flex-shrink-0">
                    <i data-lucide="{{ $isRejected ? 'x-circle' : 'ban' }}" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-heading font-semibold text-16 text-sufra-accent">
                        {{ $isRejected ? 'تم رفض الطلب من قِبل المطعم' : 'تم إلغاء الطلب' }}
                    </h3>
                    @if ($isRejected)
                        @php
                            $rejectLog = $order->statusLogs->where('to_status', \App\Enums\OrderStatus::Rejected)->first();
                        @endphp
                        <p class="text-14 text-sufra-text mt-1 leading-relaxed">
                            <strong>سبب الرفض:</strong> {{ $rejectLog?->comment ?? 'لم يتم تدوين سبب محدد.' }}
                        </p>
                    @else
                        <p class="text-14 text-sufra-muted mt-1">
                            تم إلغاء هذا الطلب بناءً على رغبتك قبل بدء التجهيز.
                        </p>
                    @endif
                </div>
            </div>
        @else
            {{-- شريط المراحل الخمس الطبيعي --}}
            <div class="relative">
                <div class="overflow-x-auto pb-2">
                    <div class="min-w-[500px]">
                        {{-- مسار الخطوط والنقاط --}}
                        <div class="relative flex items-center justify-between mb-3 px-4">
                            {{-- الخط الخلفي الرمادي الرفيع --}}
                            <div class="absolute inset-0 start-4 end-4 top-1/2 -translate-y-1/2 h-[2px] bg-sufra-border z-0"></div>

                            {{-- الخط الملون المكتمل --}}
                            @php
                                $percent = match ($currentStep) {
                                    1 => '0%',
                                    2 => '25%',
                                    3 => '50%',
                                    4 => '75%',
                                    5 => '100%',
                                    default => '0%',
                                };
                            @endphp
                            <div
                                class="absolute start-4 top-1/2 -translate-y-1/2 h-[2px] bg-sufra-primary z-0 transition-all duration-300"
                                style="width: calc({{ $percent }} - 2rem);"
                            ></div>

                            {{-- الخطوات الـ 5 كدوائر هادئة --}}
                            @php
                                $steps = [
                                    1 => ['title' => 'تم الطلب', 'desc' => 'بانتظار التأكيد'],
                                    2 => ['title' => 'قيد التجهيز', 'desc' => 'في المطبخ'],
                                    3 => ['title' => 'جاهز', 'desc' => 'جاهز للاستلام'],
                                    4 => ['title' => 'في الطريق', 'desc' => 'مع مندوب التوصيل'],
                                    5 => ['title' => 'تم التسليم', 'desc' => 'بالهناء والشفاء'],
                                ];
                            @endphp

                            @foreach ($steps as $stepNum => $stepData)
                                @php
                                    $isDone = $currentStep > $stepNum;
                                    $isCurrent = $currentStep === $stepNum;
                                @endphp
                                <div class="relative z-10 flex flex-col items-center">
                                    <div
                                        class="w-8 h-8 rounded-full border flex items-center justify-center transition-colors text-14 font-medium {{ $isDone ? 'bg-sufra-primary text-sufra-surface border-sufra-primary' : ($isCurrent ? 'bg-sufra-surface text-sufra-primary border-sufra-primary ring-2 ring-sufra-primary/20' : 'bg-sufra-bg text-sufra-muted border-sufra-border') }}"
                                    >
                                        @if ($isDone)
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        @else
                                            <span>{{ $stepNum }}</span>
                                        @endif
                                    </div>
                                    <div class="text-center mt-2">
                                        <span class="block font-heading font-medium text-14 {{ $isCurrent ? 'text-sufra-primary font-semibold' : ($isDone ? 'text-sufra-text' : 'text-sufra-muted') }}">
                                            {{ $stepData['title'] }}
                                        </span>
                                        <span class="block text-14 text-sufra-muted text-[12px]">
                                            {{ $stepData['desc'] }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- تنبيه الحيوان الآلي للتحديث الحي --}}
                <div class="mt-4 pt-3 border-t border-sufra-border flex items-center justify-between text-14 text-sufra-muted">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-sufra-primary animate-ping"></span>
                        <span>شريط المتابعة يتحدث تلقائياً مع حركة الطلب في المطعم.</span>
                    </span>
                    <span>وقت التجهيز المتوقع: <strong>{{ $order->restaurant->formatted_prep_time }}</strong></span>
                </div>
            </div>
        @endif
    </div>

    {{-- بطاقة تقييم المطعم بعد التسليم (المتطلب 1: تقييم واحد لكل طلب مسلّم) --}}
    @if ($order->canBeReviewedByCustomer())
        <div class="bg-sufra-surface border border-sufra-primary/50 rounded p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-2">
                <i data-lucide="star" class="w-5 h-5 text-sufra-primary"></i>
                <h3 class="font-heading font-semibold text-16 text-sufra-text">
                    كيف كانت تجربتك مع أطباق {{ $order->restaurant->name }}؟
                </h3>
            </div>
            <p class="text-14 text-sufra-muted">
                شاركنا رأيك الصادق لمساعدة المطعم والعملاء الآخرين. يمكنك اختيار التقييم وكتابة تعليق قصير.
            </p>

            <form wire:submit.prevent="submitReview" class="space-y-4">
                {{-- محدد النجوم من 1 إلى 5 --}}
                <div>
                    <label class="block text-14 text-sufra-muted mb-1.5 font-medium">التقييم العام بالنجوم:</label>
                    <div class="flex items-center gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                wire:click="$set('rating', {{ $i }})"
                                class="p-2 rounded border transition-colors {{ $rating >= $i ? 'bg-sufra-bg border-sufra-primary text-sufra-primary' : 'bg-sufra-surface border-sufra-border text-sufra-muted hover:border-sufra-primary/50' }}"
                                title="{{ $i }} من 5 نجوم"
                            >
                                <div class="flex items-center gap-1">
                                    <i data-lucide="star" class="w-5 h-5 {{ $rating >= $i ? 'fill-current' : '' }}"></i>
                                    <span class="text-14 font-semibold">{{ $i }}</span>
                                </div>
                            </button>
                        @endfor
                    </div>
                    <x-input-error :messages="$errors->get('rating')" class="mt-1" />
                </div>

                {{-- تعليق اختياري --}}
                <div>
                    <x-input-label for="review-comment" value="تعليق قصير أو ملاحظاتك (اختياري)" />
                    <textarea
                        id="review-comment"
                        wire:model.defer="comment"
                        rows="2"
                        placeholder="ما الذي أعجبك في الأطباق؟ جودة التحضير، الطعم، حرارة الوجبة..."
                        class="w-full rounded border border-sufra-border bg-sufra-surface px-3 py-2 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:ring-0 transition-colors resize-none mt-1"
                    ></textarea>
                    <x-input-error :messages="$errors->get('comment')" class="mt-1" />
                </div>

                <div class="flex justify-end pt-2 border-t border-sufra-border">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                    >
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>إرسال التقييم</span>
                    </button>
                </div>
            </form>
        </div>
    @elseif ($order->review)
        {{-- عرض التقييم السابق المسجل --}}
        <div class="bg-sufra-surface border border-sufra-border rounded p-5 space-y-2 text-14">
            <div class="flex items-center justify-between">
                <span class="font-heading font-semibold text-16 text-sufra-text flex items-center gap-2">
                    <i data-lucide="star" class="w-4 h-4 text-sufra-primary fill-current"></i>
                    <span>تقييمك لهذا الطلب: {{ $order->review->rating }} من 5 نجوم</span>
                </span>
                <span class="text-14 text-sufra-muted">{{ $order->review->created_at->format('Y/m/d') }}</span>
            </div>
            @if ($order->review->comment)
                <p class="text-sufra-muted italic bg-sufra-bg/40 p-2.5 rounded border border-sufra-border/60">
                    "{{ $order->review->comment }}"
                </p>
            @endif
        </div>
    @endif

    {{-- بطاقة تفاصيل التوصيل والأصناف والحساب --}}
    <div class="bg-sufra-surface border border-sufra-border rounded overflow-hidden">
        {{-- بيانات التوصيل والمطعم --}}
        <div class="p-5 sm:p-6 border-b border-sufra-border grid grid-cols-1 sm:grid-cols-2 gap-4 text-14">
            <div>
                <span class="text-sufra-muted block">مطعم الطلب:</span>
                <strong class="text-16 font-heading font-semibold text-sufra-text block mt-0.5">
                    {{ $order->restaurant->name }}
                </strong>
                <span class="text-sufra-muted flex items-center gap-1 mt-1">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-sufra-primary"></i>
                    <span>{{ $order->restaurant->district }}</span>
                </span>
            </div>

            <div>
                <span class="text-sufra-muted block">عنوان التوصيل:</span>
                @if ($order->address)
                    <strong class="text-16 font-heading font-semibold text-sufra-text block mt-0.5">
                        {{ $order->address->title }} ({{ $order->address->district }})
                    </strong>
                    <span class="text-sufra-muted block mt-1">
                        {{ $order->address->street }}
                        @if ($order->address->details)
                            &bull; {{ $order->address->details }}
                        @endif
                    </span>
                @else
                    <span class="text-sufra-muted mt-0.5 block">غير محدد</span>
                @endif
            </div>
        </div>

        {{-- الأصناف المطلوبة بأسعارها المحفوظة --}}
        <div class="p-5 sm:p-6 space-y-3">
            <h3 class="font-heading font-medium text-16 text-sufra-text border-b border-sufra-border pb-2">
                الأصناف في هذا الطلب
            </h3>

            <div class="divide-y divide-sufra-border/60">
                @foreach ($order->items as $item)
                    <div class="py-2.5 flex items-start justify-between gap-3 text-14">
                        <div>
                            <span class="font-medium text-sufra-text block">
                                {{ $item->quantity }} &times; {{ $item->name }}
                            </span>
                            @if (! empty($item->addons_list))
                                <div class="text-14 text-sufra-muted mt-0.5">
                                    مع: {{ collect($item->addons_list)->pluck('name')->implode('، ') }}
                                </div>
                            @endif
                        </div>
                        <span class="font-semibold text-sufra-text">
                            {{ $item->formatted_line_total }}
                        </span>
                    </div>
                @endforeach
            </div>

            @if ($order->notes)
                <div class="pt-2 text-14 text-sufra-muted italic">
                    ملاحظة الطلب: "{{ $order->notes }}"
                </div>
            @endif
        </div>

        {{-- ملخص الأسعار النهائي --}}
        <div class="p-5 sm:p-6 bg-sufra-bg border-t border-sufra-border space-y-1.5 text-14">
            <div class="flex justify-between text-sufra-muted">
                <span>المجموع الفرعي:</span>
                <span class="font-medium text-sufra-text">{{ $order->formatted_subtotal }}</span>
            </div>
            <div class="flex justify-between text-sufra-muted">
                <span>رسوم التوصيل:</span>
                <span class="font-medium text-sufra-text">{{ $order->formatted_delivery_fee }}</span>
            </div>
            @if ($order->discount_amount > 0)
                <div class="flex justify-between text-sufra-accent font-medium">
                    <span>خصم الكوبون:</span>
                    <span>- {{ $order->formatted_discount_amount }}</span>
                </div>
            @endif
            <div class="flex justify-between font-heading font-semibold text-16 text-sufra-text pt-2 border-t border-sufra-border">
                <span>الإجمالي:</span>
                <span class="text-20 text-sufra-primary">{{ $order->formatted_total }}</span>
            </div>
            <div class="flex justify-between text-14 text-sufra-muted pt-1">
                <span>طريقة الدفع:</span>
                <span class="font-medium text-sufra-text">{{ $order->payment_method_label }}</span>
            </div>
        </div>
    </div>

    {{-- سجل حركة الطلب التفصيلي --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-5 sm:p-6 space-y-3">
        <h3 class="font-heading font-medium text-16 text-sufra-text border-b border-sufra-border pb-2">
            سجل حركة الطلب (Timeline)
        </h3>

        <div class="space-y-2 text-14">
            @foreach ($order->statusLogs as $log)
                <div class="p-3 rounded border border-sufra-border bg-sufra-bg/40 flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-sufra-text">
                                {{ $log->to_status?->label() ?? 'الحالة' }}
                            </span>
                            <span class="text-14 text-sufra-muted">
                                ({{ $log->changed_by_name }})
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

    <div class="flex items-center justify-center gap-3 pt-2">
        <x-button href="{{ route('orders.index') }}" variant="secondary">
            العودة لقائمة طلباتي
        </x-button>
        <x-button href="{{ route('restaurants.index') }}" variant="primary">
            تصفح المزيد من المطاعم
        </x-button>
    </div>
</div>

