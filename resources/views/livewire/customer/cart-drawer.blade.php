<div
    x-data="{ open: @js($isOpen) }"
    x-on:open-cart-drawer.window="open = true"
    x-on:close-cart-drawer.window="open = false"
    x-show="open"
    x-cloak
    @keydown.escape.window="open = false; $wire.close()"
    class="relative z-50"
    role="dialog"
    aria-modal="true"
>
    {{-- خلفية معتمة هادئة (Backdrop) --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-sufra-text/40 backdrop-blur-none transition-opacity"
        @click="open = false; $wire.close()"
    ></div>

    {{-- لوحة الدرج الجانبي المنزلق --}}
    <div class="fixed inset-y-0 start-0 max-w-full flex">
        <div
            x-show="open"
            x-transition:enter="transform transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full rtl:translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full rtl:translate-x-full"
            class="w-screen max-w-md bg-sufra-surface border-e border-sufra-border flex flex-col shadow-none"
        >
            {{-- رأس الدرج --}}
            <div class="p-4 sm:p-5 border-b border-sufra-border flex items-center justify-between bg-sufra-surface">
                <div class="flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-sufra-primary"></i>
                    <h2 class="font-heading font-semibold text-20 text-sufra-text">سلة الطلبات</h2>
                </div>
                <button
                    type="button"
                    @click="open = false; $wire.close()"
                    wire:click="close"
                    class="p-1.5 rounded border border-sufra-border text-sufra-muted hover:text-sufra-text hover:bg-sufra-bg transition-colors"
                    aria-label="إغلاق السلة"
                >
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            {{-- جسم الدرج --}}
            <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4">
                @if ($isEmpty || $items->isEmpty())
                    <div class="py-12">
                        <x-empty-state
                            title="سلتك فارغة"
                            description="تصفح المطاعم الشهية وأضف أطباقك المفضلة إلى السلة لبدء طلبك."
                            icon="shopping-bag"
                        />
                    </div>
                @else
                    {{-- معلومات المطعم الحالي --}}
                    @if ($restaurant)
                        <div class="p-3 bg-sufra-bg border border-sufra-border rounded flex items-center justify-between">
                            <div class="min-w-0">
                                <span class="text-14 text-sufra-muted block">طلب من مطعم:</span>
                                <h3 class="font-heading font-semibold text-16 text-sufra-text truncate">
                                    {{ $restaurant->name }}
                                </h3>
                                <span class="text-14 text-sufra-muted flex items-center gap-1 mt-0.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-sufra-primary"></i>
                                    <span>{{ $restaurant->district }}</span>
                                </span>
                            </div>
                            <button
                                type="button"
                                wire:click="clearCart"
                                wire:confirm="هل أنت متأكد من رغبتك في تفريغ السلة؟"
                                class="text-14 text-sufra-accent hover:underline flex items-center gap-1 flex-shrink-0"
                            >
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span>تفريغ</span>
                            </button>
                        </div>
                    @endif

                    {{-- قائمة أصناف السلة --}}
                    <div class="space-y-3">
                        @foreach ($items as $item)
                            <div class="p-3.5 bg-sufra-surface border border-sufra-border rounded flex flex-col gap-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-heading font-medium text-16 text-sufra-text truncate">
                                            {{ $item->menu_item->name }}
                                        </h4>
                                        <div class="text-14 text-sufra-primary font-semibold mt-0.5">
                                            {{ $item->formatted_unit_price }} للقطعة
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="removeItem('{{ $item->key }}')"
                                        class="text-sufra-muted hover:text-sufra-accent p-1 transition-colors"
                                        title="حذف الصنف"
                                    >
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>

                                {{-- الإضافات المختارة إن وجدت --}}
                                @if ($item->addons->isNotEmpty())
                                    <div class="text-14 text-sufra-muted bg-sufra-bg/70 p-2 rounded border border-sufra-border/60 space-y-1">
                                        <span class="block text-14 font-medium text-sufra-text">الإضافات:</span>
                                        @foreach ($item->addons as $addon)
                                            <div class="flex items-center justify-between text-14">
                                                <span>+ {{ $addon->name }}</span>
                                                <span>{{ $addon->formatted_extra_price }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- الملاحظات الخاصة إن وجدت --}}
                                @if ($item->notes)
                                    <div class="text-14 text-sufra-muted flex items-start gap-1.5 italic bg-sufra-bg/40 px-2 py-1 rounded">
                                        <i data-lucide="message-square" class="w-3.5 h-3.5 mt-0.5 flex-shrink-0"></i>
                                        <span>"{{ $item->notes }}"</span>
                                    </div>
                                @endif

                                {{-- محدد الكمية وإجمالي السطر --}}
                                <div class="flex items-center justify-between pt-2 border-t border-sufra-border/60">
                                    <div class="flex items-center border border-sufra-border rounded bg-sufra-bg">
                                        <button
                                            type="button"
                                            wire:click="decrement('{{ $item->key }}')"
                                            class="w-7 h-7 flex items-center justify-center text-sufra-text hover:bg-sufra-surface transition-colors"
                                            aria-label="إنقاص الكمية"
                                        >
                                            <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <span class="w-8 text-center text-14 font-medium text-sufra-text">
                                            {{ $item->quantity }}
                                        </span>
                                        <button
                                            type="button"
                                            wire:click="increment('{{ $item->key }}')"
                                            class="w-7 h-7 flex items-center justify-center text-sufra-text hover:bg-sufra-surface transition-colors"
                                            aria-label="زيادة الكمية"
                                        >
                                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>

                                    <div class="text-16 font-semibold text-sufra-text">
                                        {{ $item->formatted_line_total }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ذيل الدرج: الحسابات والزر --}}
            @if (! $isEmpty && $items->isNotEmpty())
                <div class="p-4 sm:p-5 border-t border-sufra-border bg-sufra-surface space-y-3">
                    {{-- تفصيل الأسعار --}}
                    <div class="space-y-1.5 text-14">
                        <div class="flex items-center justify-between text-sufra-muted">
                            <span>المجموع الفرعي</span>
                            <span class="font-medium text-sufra-text">{{ number_format($subtotal, 2) }} ر.س</span>
                        </div>
                        <div class="flex items-center justify-between text-sufra-muted">
                            <span>رسوم التوصيل</span>
                            <span class="font-medium text-sufra-text">{{ number_format($deliveryFee, 2) }} ر.س</span>
                        </div>
                        <div class="flex items-center justify-between text-16 font-heading font-semibold text-sufra-text pt-2 border-t border-sufra-border">
                            <span>الإجمالي</span>
                            <span class="text-sufra-primary text-20">{{ number_format($total, 2) }} ر.س</span>
                        </div>
                    </div>

                    {{-- تنبيه الحد الأدنى للطلب --}}
                    @if (! $meetsMinOrder && $restaurant)
                        <div class="p-3 bg-sufra-bg border border-sufra-border rounded text-14 text-sufra-muted flex items-start gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-sufra-accent flex-shrink-0 mt-0.5"></i>
                            <span>
                                الحد الأدنى للطلب من هذا المطعم هو <strong>{{ $restaurant->formatted_min_order }}</strong>.
                                يتبقى لك <strong>{{ number_format($minOrderShortage, 2) }} ر.س</strong> لإتمام الطلب.
                            </span>
                        </div>
                    @endif

                    {{-- زر الانتقال لإتمام الطلب --}}
                    <div>
                        @if (! $meetsMinOrder)
                            <button
                                type="button"
                                disabled
                                class="w-full py-2.5 px-4 rounded border border-sufra-border text-14 font-medium text-sufra-muted bg-sufra-bg cursor-not-allowed text-center select-none"
                            >
                                لم تبلغ الحد الأدنى للطلب
                            </button>
                        @else
                            <a
                                href="{{ route('checkout') }}"
                                @click="open = false; $wire.close()"
                                class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded border border-transparent bg-sufra-primary text-sufra-surface text-16 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none text-center"
                            >
                                <span>متابعة إتمام الطلب</span>
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

