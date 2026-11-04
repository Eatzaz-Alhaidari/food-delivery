<div>
    {{-- رأس الصفحة --}}
    <div class="pb-4 mb-6 border-b border-sufra-border">
        <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">إتمام الطلب</h1>
        <p class="text-14 text-sufra-muted mt-1">راجع تفاصيل طلبك وحدد عنوان التوصيل وطريقة الدفع المفضلة.</p>
    </div>

    {{-- تنبيه الأخطاء العامة إن وجدت --}}
    @if ($errors->has('general'))
        <div class="mb-6 p-4 rounded border border-sufra-border bg-sufra-surface text-14 text-sufra-accent flex items-start gap-2.5">
            <i data-lucide="alert-triangle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <span>{{ $errors->first('general') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- الجانب الأيمن: تفاصيل التوصيل والدفع والملاحظات --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- القسم الأول: عنوان التوصيل --}}
            <section class="bg-sufra-surface border border-sufra-border rounded p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-sufra-border">
                    <div class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-sufra-primary"></i>
                        <h2 class="font-heading font-semibold text-16 text-sufra-text">عنوان التوصيل</h2>
                    </div>
                    <button
                        type="button"
                        wire:click="$set('showNewAddressModal', true)"
                        class="text-14 font-medium text-sufra-primary hover:underline flex items-center gap-1"
                    >
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>إضافة عنوان جديد</span>
                    </button>
                </div>

                @if ($addresses->isEmpty())
                    <div class="p-6 rounded border border-sufra-border bg-sufra-bg text-center space-y-3">
                        <p class="text-14 text-sufra-muted">ليس لديك أي عنوان توصيل مسجل حتى الآن.</p>
                        <button
                            type="button"
                            wire:click="$set('showNewAddressModal', true)"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors"
                        >
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>إضافة عنوان جديد للطلب</span>
                        </button>
                    </div>
                @else
                    <div class="space-y-2.5">
                        @foreach ($addresses as $addr)
                            <label
                                class="flex items-start gap-3 p-3.5 rounded border cursor-pointer transition-colors {{ $selectedAddressId === $addr->id ? 'border-sufra-primary bg-sufra-bg/80' : 'border-sufra-border bg-sufra-surface hover:bg-sufra-bg/40' }}"
                            >
                                <input
                                    type="radio"
                                    wire:model.live="selectedAddressId"
                                    value="{{ $addr->id }}"
                                    class="mt-1 rounded-full border-sufra-border text-sufra-primary focus:ring-sufra-primary/20 w-4 h-4"
                                >
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-heading font-semibold text-14 text-sufra-text">
                                            {{ $addr->title }}
                                        </span>
                                    </div>
                                    <div class="text-14 text-sufra-text">
                                        {{ $addr->district }} &bull; {{ $addr->street }}
                                    </div>
                                    @if ($addr->details)
                                        <div class="text-14 text-sufra-muted mt-0.5">
                                            {{ $addr->details }}
                                        </div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
                <x-input-error :messages="$errors->get('selectedAddressId')" class="mt-1" />
            </section>

            {{-- القسم الثاني: طريقة الدفع --}}
            <section class="bg-sufra-surface border border-sufra-border rounded p-5 space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-sufra-border">
                    <i data-lucide="credit-card" class="w-5 h-5 text-sufra-primary"></i>
                    <h2 class="font-heading font-semibold text-16 text-sufra-text">طريقة الدفع</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- كاش --}}
                    <label
                        class="flex items-start gap-3 p-3.5 rounded border cursor-pointer transition-colors {{ $paymentMethod === 'cash' ? 'border-sufra-primary bg-sufra-bg/80' : 'border-sufra-border bg-sufra-surface hover:bg-sufra-bg/40' }}"
                    >
                        <input
                            type="radio"
                            wire:model.live="paymentMethod"
                            value="cash"
                            class="mt-1 rounded-full border-sufra-border text-sufra-primary focus:ring-sufra-primary/20 w-4 h-4"
                        >
                        <div class="min-w-0">
                            <span class="font-heading font-semibold text-14 text-sufra-text block">
                                الدفع نقدياً (كاش)
                            </span>
                            <span class="text-14 text-sufra-muted block mt-0.5">
                                الدفع عند استلام الطلب من السائق
                            </span>
                        </div>
                    </label>

                    {{-- دفع إلكتروني تجريبي --}}
                    <label
                        class="flex items-start gap-3 p-3.5 rounded border cursor-pointer transition-colors {{ $paymentMethod === 'card_mock' ? 'border-sufra-primary bg-sufra-bg/80' : 'border-sufra-border bg-sufra-surface hover:bg-sufra-bg/40' }}"
                    >
                        <input
                            type="radio"
                            wire:model.live="paymentMethod"
                            value="card_mock"
                            class="mt-1 rounded-full border-sufra-border text-sufra-primary focus:ring-sufra-primary/20 w-4 h-4"
                        >
                        <div class="min-w-0">
                            <span class="font-heading font-semibold text-14 text-sufra-text block">
                                دفع إلكتروني تجريبي
                            </span>
                            <span class="text-14 text-sufra-muted block mt-0.5">
                                بطاقة مدى أو ائتمانية (محاكاة فورية)
                            </span>
                        </div>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('paymentMethod')" class="mt-1" />
            </section>

            {{-- القسم الثالث: ملاحظات عامة للطلب --}}
            <section class="bg-sufra-surface border border-sufra-border rounded p-5 space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b border-sufra-border">
                    <i data-lucide="message-square" class="w-5 h-5 text-sufra-primary"></i>
                    <h2 class="font-heading font-semibold text-16 text-sufra-text">ملاحظات عامة للطلب</h2>
                </div>
                <div>
                    <textarea
                        wire:model.defer="orderNotes"
                        rows="3"
                        placeholder="أي تعليمات إضافية للمطعم أو مندوب التوصيل..."
                        class="w-full rounded border border-sufra-border bg-sufra-surface px-3 py-2 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:ring-0 transition-colors resize-none"
                    ></textarea>
                    <x-input-error :messages="$errors->get('orderNotes')" class="mt-1" />
                </div>
            </section>

            {{-- القسم الرابع: كوبون الخصم (المرحلة 8) --}}
            <section class="bg-sufra-surface border border-sufra-border rounded p-5 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-sufra-border">
                    <div class="flex items-center gap-2">
                        <i data-lucide="tag" class="w-5 h-5 text-sufra-primary"></i>
                        <h2 class="font-heading font-semibold text-16 text-sufra-text">كوبون الخصم</h2>
                    </div>
                    @if ($appliedCoupon)
                        <span class="text-14 text-sufra-primary font-medium flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>كوبون مفعّل</span>
                        </span>
                    @endif
                </div>

                @if ($appliedCoupon)
                    <div class="p-3.5 rounded border border-sufra-primary/40 bg-sufra-primary/5 flex items-center justify-between gap-3">
                        <div class="space-y-0.5 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-heading font-semibold text-14 text-sufra-text tracking-wider">{{ $appliedCoupon->code }}</span>
                                <span class="text-14 text-sufra-primary font-medium">({{ $appliedCoupon->type_label }})</span>
                            </div>
                            <p class="text-14 text-sufra-muted">
                                {{ $couponSuccessMessage ?? 'تم تطبيق الكوبون وتخفيض الإجمالي.' }}
                            </p>
                        </div>
                        <button
                            type="button"
                            wire:click="removeCoupon"
                            class="px-3 py-1.5 rounded border border-sufra-border bg-sufra-surface text-14 text-sufra-accent hover:bg-sufra-bg transition-colors flex items-center gap-1 flex-shrink-0"
                        >
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>إلغاء</span>
                        </button>
                    </div>
                @else
                    <div class="space-y-2">
                        <div class="flex items-stretch gap-2">
                            <div class="relative flex-1">
                                <input
                                    type="text"
                                    wire:model="couponCode"
                                    wire:keydown.enter.prevent="applyCoupon"
                                    placeholder="أدخل كود الكوبون (مثال: SUFRA10)"
                                    class="w-full rounded border border-sufra-border bg-sufra-surface px-3 py-2 text-14 text-sufra-text uppercase placeholder-sufra-muted focus:border-sufra-primary focus:ring-0 transition-colors"
                                >
                            </div>
                            <button
                                type="button"
                                wire:click="applyCoupon"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0"
                            >
                                <span wire:loading.remove wire:target="applyCoupon">تطبيق</span>
                                <span wire:loading wire:target="applyCoupon" class="inline-flex items-center gap-1">
                                    <i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i>
                                    <span>جاري...</span>
                                </span>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('coupon')" class="mt-1" />
                    </div>
                @endif
            </section>
        </div>

        {{-- الجانب الأيسر: ملخص الطلب والأسعار وزر التأكيد --}}
        <div class="lg:col-span-5 sticky top-4">
            <div class="bg-sufra-surface border border-sufra-border rounded p-5 space-y-4">
                {{-- عنوان المطعم --}}
                <div class="pb-3 border-b border-sufra-border">
                    <span class="text-14 text-sufra-muted block">طلبك من:</span>
                    <h3 class="font-heading font-semibold text-20 text-sufra-text">
                        {{ $restaurant->name }}
                    </h3>
                    <span class="text-14 text-sufra-muted flex items-center gap-1 mt-0.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-sufra-primary"></i>
                        <span>وقت التجهيز المتوقع: {{ $restaurant->formatted_prep_time }}</span>
                    </span>
                </div>

                {{-- قائمة الأصناف المحتسبة من DB --}}
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach ($items as $item)
                        <div class="pb-3 border-b border-sufra-border/60 text-14">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="font-medium text-sufra-text block">
                                        {{ $item->quantity }} &times; {{ $item->menu_item->name }}
                                    </span>
                                    @if ($item->addons->isNotEmpty())
                                        <div class="text-14 text-sufra-muted mt-0.5">
                                            مع: {{ $item->addons->pluck('name')->implode('، ') }}
                                        </div>
                                    @endif
                                    @if ($item->notes)
                                        <div class="text-14 text-sufra-muted italic mt-0.5">
                                            "{{ $item->notes }}"
                                        </div>
                                    @endif
                                </div>
                                <span class="font-semibold text-sufra-text flex-shrink-0">
                                    {{ $item->formatted_line_total }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- تفصيل الحساب الإجمالي --}}
                <div class="space-y-2 pt-2 text-14">
                    <div class="flex items-center justify-between text-sufra-muted">
                        <span>المجموع الفرعي</span>
                        <span class="font-medium text-sufra-text">{{ number_format($subtotal, 2) }} ر.س</span>
                    </div>
                    @if ($discountAmount > 0)
                        <div class="flex items-center justify-between text-sufra-accent font-medium">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                                <span>خصم الكوبون ({{ $appliedCoupon?->code }})</span>
                            </span>
                            <span>- {{ number_format($discountAmount, 2) }} ر.س</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between text-sufra-muted">
                        <span>رسوم التوصيل</span>
                        <span class="font-medium text-sufra-text">{{ number_format($deliveryFee, 2) }} ر.س</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-sufra-border font-heading text-16 font-semibold text-sufra-text">
                        <span>الإجمالي المطلوب</span>
                        <span class="text-20 text-sufra-primary">{{ number_format($total, 2) }} ر.س</span>
                    </div>
                </div>

                {{-- زر تأكيد الطلب مع حماية منع الضغط المزدوج وإشارة التحميل --}}
                <div class="pt-2">
                    <button
                        type="button"
                        wire:click="submitOrder"
                        wire:loading.attr="disabled"
                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded border border-transparent bg-sufra-primary text-sufra-surface text-16 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="submitOrder">تأكيد وإرسال الطلب</span>
                        <span wire:loading wire:target="submitOrder" class="inline-flex items-center gap-2">
                            <i data-lucide="loader" class="w-4 h-4 animate-spin"></i>
                            <span>جاري معالجة طلبك...</span>
                        </span>
                        <i data-lucide="check" class="w-4 h-4" wire:loading.remove wire:target="submitOrder"></i>
                    </button>
                    <p class="text-14 text-sufra-muted text-center mt-2">
                        بالضغط على تأكيد، فإنك توافق على شروط الطلب والتوصيل.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- نافذة إضافة عنوان جديد سريعة أثناء الدفع --}}
    @if ($showNewAddressModal)
        <div
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="checkout-address-modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-sufra-text/40 transition-opacity" wire:click="$set('showNewAddressModal', false)"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-sufra-surface border border-sufra-border rounded shadow-none p-5 sm:p-6 my-8 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-sufra-border">
                        <h3 id="checkout-address-modal-title" class="font-heading font-semibold text-16 text-sufra-text">
                            إضافة عنوان توصيل جديد
                        </h3>
                        <button
                            type="button"
                            wire:click="$set('showNewAddressModal', false)"
                            class="p-1 rounded text-sufra-muted hover:text-sufra-text"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveNewAddress" class="space-y-4">
                        <div>
                            <x-input-label for="new-title" value="تسمية العنوان (مثل: المنزل، العمل)" />
                            <x-text-input
                                id="new-title"
                                type="text"
                                wire:model.defer="newTitle"
                                class="w-full mt-1"
                                placeholder="مثال: المنزل"
                            />
                            <x-input-error :messages="$errors->get('newTitle')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="new-district" value="الحي" />
                            <x-text-input
                                id="new-district"
                                type="text"
                                wire:model.defer="newDistrict"
                                class="w-full mt-1"
                                placeholder="مثال: حطين، النرجس..."
                            />
                            <x-input-error :messages="$errors->get('newDistrict')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="new-street" value="الشارع" />
                            <x-text-input
                                id="new-street"
                                type="text"
                                wire:model.defer="newStreet"
                                class="w-full mt-1"
                                placeholder="مثال: شارع التخصصي"
                            />
                            <x-input-error :messages="$errors->get('newStreet')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="new-details" value="تفاصيل إضافية (اختياري)" />
                            <textarea
                                id="new-details"
                                wire:model.defer="newDetails"
                                rows="2"
                                placeholder="رقم الشقة، المعلم القريب..."
                                class="w-full rounded border border-sufra-border bg-sufra-surface px-3 py-2 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:ring-0 transition-colors resize-none mt-1"
                            ></textarea>
                            <x-input-error :messages="$errors->get('newDetails')" class="mt-1" />
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-sufra-border">
                            <button
                                type="button"
                                wire:click="$set('showNewAddressModal', false)"
                                class="px-4 py-2 rounded border border-sufra-border text-14 font-medium text-sufra-text bg-sufra-surface hover:bg-sufra-bg transition-colors"
                            >
                                إلغاء
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                            >
                                حفظ واستخدام
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

