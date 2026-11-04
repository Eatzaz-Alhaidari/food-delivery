<div class="space-y-6">
    {{-- رأس الصفحة --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-sufra-border">
        <div>
            <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">إدارة الكوبونات</h1>
            <p class="text-14 text-sufra-muted mt-1">إنشاء كوبونات الخصم وتحديد قيمها وشروطها وتاريخ صلاحيتها.</p>
        </div>
        <button
            type="button"
            wire:click="openCreateModal"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
        >
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>إضافة كوبون جديد</span>
        </button>
    </div>

    {{-- رسالة نجاح العملية --}}
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

    {{-- شريط البحث --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-4">
        <div class="relative max-w-md">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-sufra-muted">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="ابحث بكود الكوبون..."
                class="w-full rounded border border-sufra-border bg-sufra-bg ps-9 pe-4 py-2 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:bg-sufra-surface focus:ring-0 transition-colors"
            >
        </div>
    </div>

    {{-- جدول الكوبونات (المتطلب 3 و 4) --}}
    @if ($coupons->isEmpty())
        <div class="py-8">
            <x-empty-state
                title="لا توجد كوبونات مسجلة"
                description="لم تقم بإضافة أي كوبونات بعد. أنشئ كوبونك الترويجي الأول لجذب المزيد من الطلبات."
                icon="tag"
            />
        </div>
    @else
        <div class="bg-sufra-surface border border-sufra-border rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-start text-14">
                    <thead class="bg-sufra-bg border-b border-sufra-border text-sufra-muted font-medium">
                        <tr>
                            <th class="p-3.5 text-start">كود الكوبون</th>
                            <th class="p-3.5 text-start">النوع والقيمة</th>
                            <th class="p-3.5 text-start">الحد الأدنى</th>
                            <th class="p-3.5 text-start">مرات الاستخدام</th>
                            <th class="p-3.5 text-start">تاريخ الانتهاء</th>
                            <th class="p-3.5 text-start">الحالة</th>
                            <th class="p-3.5 text-end">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sufra-border/60">
                        @foreach ($coupons as $coupon)
                            <tr class="hover:bg-sufra-bg/40 transition-colors">
                                {{-- الكود --}}
                                <td class="p-3.5">
                                    <span class="font-heading font-semibold text-16 text-sufra-primary px-2.5 py-1 rounded bg-sufra-bg border border-sufra-border inline-block tracking-wider">
                                        {{ $coupon->code }}
                                    </span>
                                </td>

                                {{-- القيمة --}}
                                <td class="p-3.5">
                                    <strong class="text-sufra-text block font-medium">
                                        {{ $coupon->formatted_value }}
                                    </strong>
                                    <span class="text-14 text-sufra-muted block">
                                        {{ $coupon->type_label }}
                                    </span>
                                </td>

                                {{-- الحد الأدنى --}}
                                <td class="p-3.5 text-sufra-muted">
                                    {{ $coupon->min_order ? number_format($coupon->min_order, 2) . ' ر.س' : 'بدون حد أدنى' }}
                                </td>

                                {{-- الاستخدام --}}
                                <td class="p-3.5">
                                    <span class="font-medium text-sufra-text">{{ $coupon->used_count }}</span>
                                    <span class="text-sufra-muted text-14">/ {{ $coupon->max_uses ? $coupon->max_uses . ' مرة' : 'غير محدود' }}</span>
                                </td>

                                {{-- تاريخ الانتهاء --}}
                                <td class="p-3.5">
                                    @if ($coupon->expires_at)
                                        <span class="{{ $coupon->expires_at->isPast() ? 'text-sufra-accent font-medium' : 'text-sufra-muted' }}">
                                            {{ $coupon->expires_at->format('Y/m/d') }}
                                            @if ($coupon->expires_at->isPast())
                                                (منتهي)
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-sufra-muted">صالح دائماً</span>
                                    @endif
                                </td>

                                {{-- زر التفعيل والتعطيل --}}
                                <td class="p-3.5">
                                    <button
                                        type="button"
                                        wire:click="toggleActive({{ $coupon->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded text-14 font-medium transition-colors border {{ $coupon->is_active ? 'border-sufra-border bg-sufra-bg text-sufra-primary hover:bg-sufra-surface' : 'border-sufra-border bg-sufra-surface text-sufra-muted hover:bg-sufra-bg' }}"
                                        title="اضغط للتبديل الفوري"
                                    >
                                        <span class="w-2 h-2 rounded-full {{ $coupon->is_active ? 'bg-sufra-primary' : 'bg-sufra-muted' }}"></span>
                                        <span>{{ $coupon->is_active ? 'مفعّل' : 'معطّل' }}</span>
                                    </button>
                                </td>

                                {{-- التعديل --}}
                                <td class="p-3.5 text-end">
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $coupon->id }})"
                                        class="p-2 rounded border border-sufra-border text-sufra-muted hover:text-sufra-text hover:bg-sufra-bg transition-colors"
                                        title="تعديل الكوبون"
                                    >
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-sufra-border">
                {{ $coupons->links() }}
            </div>
        </div>
    @endif

    {{-- نافذة إضافة أو تعديل كوبون --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-sufra-text/40 transition-opacity" wire:click="closeModal"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-sufra-surface border border-sufra-border rounded shadow-none p-5 sm:p-6 my-8 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-sufra-border">
                        <h3 class="font-heading font-semibold text-16 text-sufra-text">
                            {{ $editingCouponId ? 'تعديل الكوبون' : 'إضافة كوبون خصم جديد' }}
                        </h3>
                        <button type="button" wire:click="closeModal" class="p-1 rounded text-sufra-muted hover:text-sufra-text">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveCoupon" class="space-y-4">
                        <div>
                            <x-input-label for="coupon-code" value="كود الكوبون (بالإنجليزية وبدون مسافات)" />
                            <x-text-input
                                id="coupon-code"
                                type="text"
                                wire:model.defer="code"
                                class="w-full mt-1 uppercase"
                                placeholder="مثال: SUFRA10 أو RAMADAN"
                            />
                            <x-input-error :messages="$errors->get('code')" class="mt-1" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="coupon-type" value="نوع الخصم" />
                                <select
                                    id="coupon-type"
                                    wire:model.live="type"
                                    class="w-full rounded border border-sufra-border bg-sufra-surface px-3 py-2 text-14 text-sufra-text focus:border-sufra-primary focus:ring-0 transition-colors mt-1"
                                >
                                    <option value="percent">نسبة مئوية (%)</option>
                                    <option value="fixed">مبلغ ثابت (ر.س)</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="coupon-value" value="قيمة الخصم" />
                                <x-text-input
                                    id="coupon-value"
                                    type="number"
                                    step="0.5"
                                    wire:model.defer="value"
                                    class="w-full mt-1"
                                />
                                <x-input-error :messages="$errors->get('value')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="coupon-min" value="الحد الأدنى للطلب (اختياري)" />
                                <x-text-input
                                    id="coupon-min"
                                    type="number"
                                    step="1"
                                    wire:model.defer="minOrder"
                                    class="w-full mt-1"
                                    placeholder="بدون حد"
                                />
                                <x-input-error :messages="$errors->get('minOrder')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="coupon-max-uses" value="أقصى عدد استخدامات (اختياري)" />
                                <x-text-input
                                    id="coupon-max-uses"
                                    type="number"
                                    wire:model.defer="maxUses"
                                    class="w-full mt-1"
                                    placeholder="غير محدود"
                                />
                                <x-input-error :messages="$errors->get('maxUses')" class="mt-1" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="coupon-expires" value="تاريخ انتهاء الصلاحية (اختياري)" />
                            <x-text-input
                                id="coupon-expires"
                                type="date"
                                wire:model.defer="expiresAt"
                                class="w-full mt-1"
                            />
                            <x-input-error :messages="$errors->get('expiresAt')" class="mt-1" />
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <input
                                id="coupon-active"
                                type="checkbox"
                                wire:model.defer="isActive"
                                class="rounded border-sufra-border text-sufra-primary focus:ring-sufra-primary/20 w-4 h-4"
                            >
                            <label for="coupon-active" class="text-14 font-medium text-sufra-text cursor-pointer">
                                تفعيل الكوبون للعملاء فور الحفظ
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-sufra-border">
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 rounded border border-sufra-border text-14 font-medium text-sufra-text bg-sufra-surface hover:bg-sufra-bg transition-colors"
                            >
                                إلغاء
                            </button>
                            <button
                                type="submit"
                                class="px-5 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                            >
                                {{ $editingCouponId ? 'حفظ التعديلات' : 'إضافة الكوبون' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

