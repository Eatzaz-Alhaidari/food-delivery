<div>
    {{-- قسم الغلاف والشعار ومعلومات المطعم الأساسية --}}
    <div class="bg-sufra-surface border border-sufra-border rounded overflow-hidden mb-6">
        {{-- صورة الغلاف --}}
        <div class="h-36 sm:h-52 w-full bg-sufra-bg relative overflow-hidden border-b border-sufra-border">
            <img src="{{ $restaurant->cover_url }}" alt="غلاف {{ $restaurant->name }}" class="w-full h-full object-cover">
        </div>

        {{-- بيانات المطعم وتفاصيل التوصيل --}}
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                {{-- شعار المطعم --}}
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded border border-sufra-border overflow-hidden bg-sufra-bg flex-shrink-0 -mt-12 sm:-mt-14 relative z-10">
                    <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                </div>

                {{-- التفاصيل والعناوين --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">
                            {{ $restaurant->name }}
                        </h1>
                        <x-badge variant="neutral">{{ $restaurant->cuisine }}</x-badge>
                        {{-- شارة التقييم وعدد المقيمين (المتطلب 2) --}}
                        <span class="inline-flex items-center gap-1 text-14 text-sufra-primary font-medium bg-sufra-bg px-2.5 py-0.5 rounded border border-sufra-border">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                            <span>{{ $restaurant->average_rating }}</span>
                            <span class="text-sufra-muted text-[12px]">({{ $restaurant->reviews_count }} تقييم)</span>
                        </span>
                        @if ($restaurant->is_open)
                            <x-badge variant="primary" icon="check-circle">مفتوح للطلب</x-badge>
                        @else
                            <x-badge variant="neutral" icon="clock">مغلق حالياً</x-badge>
                        @endif
                    </div>

                    @if ($restaurant->description)
                        <p class="text-14 sm:text-16 text-sufra-muted mb-4 max-w-2xl leading-relaxed">
                            {{ $restaurant->description }}
                        </p>
                    @endif

                    {{-- شريط معلومات التوصيل والحي --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-sufra-border text-14">
                        <div>
                            <span class="text-sufra-muted block text-14">الحي</span>
                            <span class="font-medium text-sufra-text flex items-center gap-1 mt-0.5">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-sufra-primary"></i>
                                <span>{{ $restaurant->district }}</span>
                            </span>
                        </div>
                        <div>
                            <span class="text-sufra-muted block text-14">وقت التجهيز</span>
                            <span class="font-medium text-sufra-text flex items-center gap-1 mt-0.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-sufra-primary"></i>
                                <span>{{ $restaurant->formatted_prep_time }}</span>
                            </span>
                        </div>
                        <div>
                            <span class="text-sufra-muted block text-14">رسوم التوصيل</span>
                            <span class="font-medium text-sufra-text flex items-center gap-1 mt-0.5">
                                <i data-lucide="truck" class="w-3.5 h-3.5 text-sufra-primary"></i>
                                <span>{{ $restaurant->formatted_delivery_fee }}</span>
                            </span>
                        </div>
                        <div>
                            <span class="text-sufra-muted block text-14">الحد الأدنى</span>
                            <span class="font-medium text-sufra-text flex items-center gap-1 mt-0.5">
                                <i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-sufra-primary"></i>
                                <span>{{ $restaurant->formatted_min_order }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- تنبيه إغلاق المطعم إن كان مغلقاً (المتطلب 4) --}}
            @if (! $restaurant->is_open)
                <div class="mt-4 p-3.5 bg-sufra-bg border border-sufra-border rounded text-14 text-sufra-muted flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-sufra-accent flex-shrink-0"></i>
                    <span>المطعم مغلق حالياً ولا يستقبل طلبات جديدة. يمكنك تصفح الأصناف والأسعار بحرية.</span>
                </div>
            @endif
        </div>
    </div>

    {{-- شريط تصنيفات ثابت أثناء التمرير (Sticky Categories Bar) بحسب المتطلب 3 --}}
    @if ($restaurant->categories->isNotEmpty())
        <div class="sticky top-0 z-20 bg-sufra-surface border-y border-sufra-border -mx-4 sm:-mx-6 px-4 sm:px-6 py-2.5 mb-6 overflow-x-auto scrollbar-none">
            <div class="max-w-sufra mx-auto flex items-center gap-2">
                <span class="text-14 text-sufra-muted me-1 hidden sm:inline">الأقسام:</span>
                @foreach ($restaurant->categories as $category)
                    <a
                        href="#category-{{ $category->id }}"
                        class="px-3.5 py-1.5 rounded text-14 font-medium border border-sufra-border bg-sufra-bg hover:bg-sufra-surface hover:border-sufra-primary text-sufra-text transition-colors duration-150 flex-shrink-0"
                    >
                        {{ $category->name }}
                        <span class="text-14 text-sufra-muted ms-1">({{ $category->menuItems->count() }})</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- قائمة أصناف المنيو مقسمة بالتصنيفات --}}
    @if ($restaurant->categories->isEmpty())
        <x-empty-state
            title="قائمة الطعام غير متوفرة حالياً"
            description="لم يقم المطعم بإضافة أي تصنيفات أو أصناف بعد."
            icon="utensils"
        />
    @else
        <div class="space-y-8">
            @foreach ($restaurant->categories as $category)
                <section id="category-{{ $category->id }}" class="scroll-mt-16">
                    {{-- عنوان التصنيف --}}
                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-sufra-border">
                        <h2 class="font-heading font-semibold text-20 text-sufra-text">
                            {{ $category->name }}
                        </h2>
                        <span class="text-14 text-sufra-muted">{{ $category->menuItems->count() }} أصناف</span>
                    </div>

                    {{-- الأصناف كصفوف أفقية مريحة للعين بحسب قواعد DESIGN.md --}}
                    @if ($category->menuItems->isEmpty())
                        <div class="p-6 rounded border border-sufra-border bg-sufra-surface text-center text-14 text-sufra-muted">
                            لا توجد أصناف مسجلة في هذا القسم حالياً.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($category->menuItems as $item)
                                @php
                                    $isUnavailable = ! $item->is_available;
                                    $cannotOrder = $isUnavailable || ! $restaurant->is_open;
                                @endphp
                                <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 transition-colors {{ $isUnavailable ? 'opacity-55 bg-sufra-bg/70' : 'hover:border-sufra-primary/50' }}">
                                    {{-- يمين: صورة الصنف + البيانات --}}
                                    <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                                        <div class="w-20 h-20 sm:w-24 sm:h-20 rounded border border-sufra-border overflow-hidden bg-sufra-bg flex-shrink-0">
                                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                                <h3 class="font-heading font-semibold text-16 text-sufra-text truncate">
                                                    {{ $item->name }}
                                                </h3>
                                                @if ($isUnavailable)
                                                    <x-badge variant="neutral">نفد مؤقتاً</x-badge>
                                                @endif
                                            </div>

                                            @if ($item->description)
                                                <p class="text-14 text-sufra-muted line-clamp-2 mb-2 leading-relaxed">
                                                    {{ $item->description }}
                                                </p>
                                            @endif

                                            {{-- السعر والإضافات --}}
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="font-semibold text-16 text-sufra-primary">
                                                    {{ $item->formatted_price }}
                                                </span>

                                                @if ($item->addons->isNotEmpty())
                                                    <span class="text-14 text-sufra-muted border-s border-sufra-border ps-2 flex items-center gap-1">
                                                        <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-sufra-muted"></i>
                                                        <span>{{ $item->addons->count() }} إضافات اختيارية</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- يسار: زر الإضافة للطلب --}}
                                    <div class="flex items-center justify-end pt-2 sm:pt-0 border-t sm:border-t-0 border-sufra-border flex-shrink-0">
                                        @if ($cannotOrder)
                                            <button
                                                type="button"
                                                disabled
                                                class="px-4 py-2 rounded border border-sufra-border text-14 font-medium text-sufra-muted bg-sufra-bg cursor-not-allowed select-none"
                                            >
                                                {{ ! $restaurant->is_open ? 'المطعم مغلق' : 'نفد مؤقتاً' }}
                                            </button>
                                        @else
                                            <button
                                                type="button"
                                                wire:click="openItemModal({{ $item->id }})"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                                            >
                                                <i data-lucide="plus" class="w-4 h-4"></i>
                                                <span>أضف للطلب</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    @endif

    {{-- قسم آراء وتقييمات العملاء (المتطلب 2: عرض آخر التعليقات ومتوسط التقييم) --}}
    <div class="mt-12 pt-8 border-t border-sufra-border space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-2 border-b border-sufra-border">
            <div class="flex items-center gap-2">
                <i data-lucide="star" class="w-5 h-5 text-sufra-primary fill-current"></i>
                <h2 class="font-heading font-semibold text-20 text-sufra-text">تقييمات وآراء العملاء</h2>
            </div>
            <div class="flex items-center gap-2 text-14">
                <span class="text-sufra-muted">متوسط التقييم العام:</span>
                <strong class="font-heading font-semibold text-16 text-sufra-primary">
                    {{ $restaurant->average_rating }} من 5
                </strong>
                <span class="text-sufra-muted">({{ $restaurant->reviews_count }} تقييم)</span>
            </div>
        </div>

        @if ($restaurant->reviews->isEmpty())
            <div class="p-6 rounded border border-sufra-border bg-sufra-surface text-center text-14 text-sufra-muted">
                لا توجد تقييمات مسجلة لهذا المطعم حتى الآن. كن أول من يطلب منه ويشارك تجربته!
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($restaurant->reviews->take(6) as $rev)
                    <div class="bg-sufra-surface border border-sufra-border rounded p-4 space-y-2">
                        <div class="flex items-center justify-between text-14">
                            <strong class="font-medium text-sufra-text">{{ $rev->user->name }}</strong>
                            <span class="text-sufra-muted">{{ $rev->created_at->diffForHumans() }}</span>
                        </div>

                        {{-- النجوم الملونة --}}
                        <div class="flex items-center gap-1 text-sufra-primary">
                            @for ($s = 1; $s <= 5; $s++)
                                <i data-lucide="star" class="w-4 h-4 {{ $s <= $rev->rating ? 'fill-current' : 'text-sufra-border' }}"></i>
                            @endfor
                            <span class="text-14 font-semibold ms-1">{{ $rev->rating }} / 5</span>
                        </div>

                        @if ($rev->comment)
                            <p class="text-14 text-sufra-muted leading-relaxed italic bg-sufra-bg/40 p-2 rounded">
                                "{{ $rev->comment }}"
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- نافذة اختيار الإضافات والملاحظة والكمية (المتطلب 4) --}}
    @if ($showItemModal && $selectedItem)
        <div
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="item-modal-title"
            role="dialog"
            aria-modal="true"
        >
            {{-- خلفية معتمة هادئة --}}
            <div class="fixed inset-0 bg-sufra-text/40 transition-opacity" wire:click="closeItemModal"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-sufra-surface border border-sufra-border rounded shadow-none overflow-hidden my-8">
                    {{-- رأس النافذة --}}
                    <div class="p-4 sm:p-5 border-b border-sufra-border flex items-start justify-between gap-3 bg-sufra-surface">
                        <div class="min-w-0">
                            <h3 id="item-modal-title" class="font-heading font-semibold text-20 text-sufra-text">
                                {{ $selectedItem->name }}
                            </h3>
                            <div class="text-16 font-semibold text-sufra-primary mt-1">
                                {{ $selectedItem->formatted_price }}
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="closeItemModal"
                            class="p-1.5 rounded border border-sufra-border text-sufra-muted hover:text-sufra-text hover:bg-sufra-bg transition-colors flex-shrink-0"
                            aria-label="إغلاق"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    {{-- محتوى النافذة --}}
                    <div class="p-4 sm:p-5 space-y-5 max-h-[70vh] overflow-y-auto">
                        @if ($selectedItem->description)
                            <p class="text-14 text-sufra-muted leading-relaxed">
                                {{ $selectedItem->description }}
                            </p>
                        @endif

                        {{-- قسم الإضافات إن وُجدت --}}
                        @if ($selectedItem->addons->isNotEmpty())
                            <div class="space-y-2.5">
                                <span class="font-heading font-medium text-16 text-sufra-text block">
                                    الإضافات الاختيارية:
                                </span>
                                <div class="space-y-2">
                                    @foreach ($selectedItem->addons as $addon)
                                        <label
                                            class="flex items-center justify-between p-3 rounded border border-sufra-border bg-sufra-bg/40 hover:bg-sufra-bg cursor-pointer transition-colors"
                                        >
                                            <div class="flex items-center gap-2.5">
                                                <input
                                                    type="checkbox"
                                                    wire:model.live="selectedAddons"
                                                    value="{{ $addon->id }}"
                                                    class="rounded border-sufra-border text-sufra-primary focus:ring-sufra-primary/20 w-4 h-4"
                                                >
                                                <span class="text-14 font-medium text-sufra-text">{{ $addon->name }}</span>
                                            </div>
                                            <span class="text-14 font-semibold text-sufra-primary">
                                                + {{ $addon->formatted_extra_price }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- قسم الملاحظات الخاصة --}}
                        <div class="space-y-1.5">
                            <label for="item-notes" class="font-heading font-medium text-14 text-sufra-text block">
                                ملاحظات خاصة بالطبق (اختياري):
                            </label>
                            <textarea
                                id="item-notes"
                                wire:model.defer="notes"
                                rows="2"
                                placeholder="مثال: بدون بصل، زيادة صوص، الصوص جانبي..."
                                class="w-full rounded border border-sufra-border bg-sufra-surface px-3 py-2 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:ring-0 transition-colors resize-none"
                            ></textarea>
                        </div>

                        {{-- قسم تحديد الكمية --}}
                        <div class="pt-3 border-t border-sufra-border flex items-center justify-between">
                            <span class="font-heading font-medium text-16 text-sufra-text">الكمية:</span>
                            <div class="flex items-center border border-sufra-border rounded bg-sufra-bg">
                                <button
                                    type="button"
                                    wire:click="decrementQuantity"
                                    class="w-9 h-9 flex items-center justify-center text-sufra-text hover:bg-sufra-surface transition-colors"
                                    aria-label="إنقاص الكمية"
                                >
                                    <i data-lucide="minus" class="w-4 h-4"></i>
                                </button>
                                <span class="w-10 text-center text-16 font-semibold text-sufra-text">
                                    {{ $quantity }}
                                </span>
                                <button
                                    type="button"
                                    wire:click="incrementQuantity"
                                    class="w-9 h-9 flex items-center justify-center text-sufra-text hover:bg-sufra-surface transition-colors"
                                    aria-label="زيادة الكمية"
                                >
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ذيل النافذة: الإجمالي وزر الإضافة --}}
                    <div class="p-4 sm:p-5 border-t border-sufra-border bg-sufra-bg/40 flex items-center justify-between gap-3">
                        <div>
                            <span class="text-14 text-sufra-muted block">الإجمالي:</span>
                            <span class="text-20 font-heading font-semibold text-sufra-primary">
                                {{ number_format($this->itemModalTotal, 2) }} ر.س
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                wire:click="closeItemModal"
                                class="px-4 py-2 rounded border border-sufra-border text-14 font-medium text-sufra-text bg-sufra-surface hover:bg-sufra-bg transition-colors"
                            >
                                إلغاء
                            </button>
                            <button
                                type="button"
                                wire:click="addToCart"
                                class="inline-flex items-center gap-1.5 px-5 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                            >
                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                <span>أضف إلى السلة</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- نافذة تأكيد تفريغ السلة عند تعارض المطاعم (المتطلب 2) --}}
    @if ($showClearCartConfirmModal)
        <div
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="clear-cart-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-sufra-text/40 transition-opacity" wire:click="cancelClearCart"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-sufra-surface border border-sufra-border rounded shadow-none p-5 sm:p-6 my-8 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded border border-sufra-border bg-sufra-bg flex items-center justify-center text-sufra-accent flex-shrink-0">
                            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 id="clear-cart-title" class="font-heading font-semibold text-16 text-sufra-text">
                                تفريغ السلة الحالية؟
                            </h3>
                            <p class="text-14 text-sufra-muted mt-1 leading-relaxed">
                                سلتك الحالية تحتوي على أصناف من <strong>{{ $previousRestaurantName }}</strong>.
                                السلة الواحدة تدعم طلباً من مطعم واحد فقط. هل ترغب في تفريغ السلة والبدء من <strong>{{ $restaurant->name }}</strong>؟
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-sufra-border">
                        <button
                            type="button"
                            wire:click="cancelClearCart"
                            class="px-4 py-2 rounded border border-sufra-border text-14 font-medium text-sufra-text bg-sufra-surface hover:bg-sufra-bg transition-colors"
                        >
                            إلغاء والاحتفاظ بالسلة
                        </button>
                        <button
                            type="button"
                            wire:click="confirmClearCartAndContinue"
                            class="px-4 py-2 rounded border border-transparent bg-sufra-accent text-sufra-surface text-14 font-medium hover:bg-sufra-accent/90 transition-colors shadow-none"
                        >
                            تفريغ السلة والمتابعة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
