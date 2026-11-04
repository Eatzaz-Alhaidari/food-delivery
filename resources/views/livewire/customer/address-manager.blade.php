<div>
    {{-- رأس الصفحة --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 mb-6 border-b border-sufra-border">
        <div>
            <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">عناوين التوصيل</h1>
            <p class="text-14 text-sufra-muted mt-1">إدارة عناوينك المسجلة لتسهيل وسرعة إتمام طلباتك.</p>
        </div>
        <div>
            <button
                type="button"
                wire:click="openCreateModal"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
            >
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>إضافة عنوان جديد</span>
            </button>
        </div>
    </div>

    {{-- رسالة نجاح هادئة --}}
    @if ($successMessage)
        <div class="mb-6 p-4 rounded border border-sufra-border bg-sufra-surface text-14 text-sufra-primary flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-sufra-primary"></i>
                <span>{{ $successMessage }}</span>
            </div>
            <button type="button" wire:click="$set('successMessage', null)" class="text-sufra-muted hover:text-sufra-text">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    {{-- قائمة العناوين أو الحالة الفارغة --}}
    @if ($addresses->isEmpty())
        <div class="py-8">
            <x-empty-state
                title="لا توجد عناوين محفوظة"
                description="لم تسجل أي عنوان توصيل حتى الآن. أضف عنوانك الأول لتتمكن من إتمام الطلبات بسهولة."
                icon="map-pin"
            >
                <x-slot name="action">
                    <button
                        type="button"
                        wire:click="openCreateModal"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors"
                    >
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>إضافة عنوانك الأول</span>
                    </button>
                </x-slot>
            </x-empty-state>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($addresses as $address)
                <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex flex-col justify-between gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-14 font-medium border border-sufra-border bg-sufra-bg text-sufra-text">
                                <i data-lucide="bookmark" class="w-3.5 h-3.5 text-sufra-primary"></i>
                                <span>{{ $address->title }}</span>
                            </span>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    wire:click="editAddress({{ $address->id }})"
                                    class="text-14 text-sufra-muted hover:text-sufra-text p-1 transition-colors"
                                    title="تعديل العنوان"
                                >
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteAddress({{ $address->id }})"
                                    wire:confirm="هل أنت متأكد من حذف هذا العنوان؟"
                                    class="text-14 text-sufra-muted hover:text-sufra-accent p-1 transition-colors"
                                    title="حذف العنوان"
                                >
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-16 font-medium text-sufra-text flex items-center gap-1.5">
                            <i data-lucide="map-pin" class="w-4 h-4 text-sufra-primary flex-shrink-0"></i>
                            <span>{{ $address->district }} &bull; {{ $address->street }}</span>
                        </div>

                        @if ($address->details)
                            <p class="text-14 text-sufra-muted mt-2 ps-5.5 leading-relaxed">
                                {{ $address->details }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- نافذة إضافة / تعديل عنوان --}}
    @if ($showModal)
        <div
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="address-modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-sufra-text/40 transition-opacity" wire:click="closeModal"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-sufra-surface border border-sufra-border rounded shadow-none p-5 sm:p-6 my-8 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-sufra-border">
                        <h3 id="address-modal-title" class="font-heading font-semibold text-16 text-sufra-text">
                            {{ $editingAddressId ? 'تعديل العنوان' : 'إضافة عنوان توصيل جديد' }}
                        </h3>
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="p-1 rounded text-sufra-muted hover:text-sufra-text"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveAddress" class="space-y-4">
                        <div>
                            <x-input-label for="modal-title" value="تسمية العنوان (مثل: المنزل، المكتب)" />
                            <x-text-input
                                id="modal-title"
                                type="text"
                                wire:model.defer="title"
                                class="w-full mt-1"
                                placeholder="مثال: البيت"
                            />
                            <x-input-error :messages="$errors->get('title')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="modal-district" value="الحي" />
                            <x-text-input
                                id="modal-district"
                                type="text"
                                wire:model.defer="district"
                                class="w-full mt-1"
                                placeholder="مثال: حطين، الياسمين، النرجس..."
                            />
                            <x-input-error :messages="$errors->get('district')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="modal-street" value="الشارع" />
                            <x-text-input
                                id="modal-street"
                                type="text"
                                wire:model.defer="street"
                                class="w-full mt-1"
                                placeholder="مثال: طريق الأمير تركي بن عبدالعزيز"
                            />
                            <x-input-error :messages="$errors->get('street')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="modal-details" value="تفاصيل إضافية (اختياري)" />
                            <textarea
                                id="modal-details"
                                wire:model.defer="details"
                                rows="2"
                                placeholder="رقم الشقة، الدور، بجوار مسجد..."
                                class="w-full rounded border border-sufra-border bg-sufra-surface px-3 py-2 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:ring-0 transition-colors resize-none mt-1"
                            ></textarea>
                            <x-input-error :messages="$errors->get('details')" class="mt-1" />
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
                                class="px-4 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                            >
                                {{ $editingAddressId ? 'حفظ التعديلات' : 'إضافة العنوان' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

