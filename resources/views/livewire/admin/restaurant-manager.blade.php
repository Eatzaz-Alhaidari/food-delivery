<div class="space-y-6">
    {{-- رأس الصفحة --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-sufra-border">
        <div>
            <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">إدارة المطاعم</h1>
            <p class="text-14 text-sufra-muted mt-1">إضافة مطابخ ومطاعم جديدة، وإنشاء حسابات الملاك، وتفعيل وإيقاف الخدمة.</p>
        </div>
        <button
            type="button"
            wire:click="openCreateModal"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
        >
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>إضافة مطعم جديد</span>
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

    {{-- شريط البحث والفلترة --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-sufra-muted">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="ابحث باسم المطعم أو الحي أو نوع المطبخ..."
                class="w-full rounded border border-sufra-border bg-sufra-bg ps-9 pe-4 py-2 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:bg-sufra-surface focus:ring-0 transition-colors"
            >
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                wire:click="$set('statusFilter', 'all')"
                class="px-3 py-1.5 rounded text-14 font-medium border transition-colors {{ $statusFilter === 'all' ? 'bg-sufra-primary text-sufra-surface border-transparent' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
            >
                جميع المطاعم
            </button>
            <button
                type="button"
                wire:click="$set('statusFilter', 'active')"
                class="px-3 py-1.5 rounded text-14 font-medium border transition-colors {{ $statusFilter === 'active' ? 'bg-sufra-primary text-sufra-surface border-transparent' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
            >
                نشطة فقط
            </button>
            <button
                type="button"
                wire:click="$set('statusFilter', 'inactive')"
                class="px-3 py-1.5 rounded text-14 font-medium border transition-colors {{ $statusFilter === 'inactive' ? 'bg-sufra-primary text-sufra-surface border-transparent' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
            >
                معطلة
            </button>
        </div>
    </div>

    {{-- جدول المطاعم --}}
    @if ($restaurants->isEmpty())
        <div class="py-8">
            <x-empty-state
                title="لا توجد مطاعم مطابقة للبحث"
                description="جرّب تعديل كلمات البحث أو الفلاتر المختارة."
                icon="store"
            />
        </div>
    @else
        <div class="bg-sufra-surface border border-sufra-border rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-start text-14">
                    <thead class="bg-sufra-bg border-b border-sufra-border text-sufra-muted font-medium">
                        <tr>
                            <th class="p-3.5 text-start">المطعم</th>
                            <th class="p-3.5 text-start">المطبخ والحي</th>
                            <th class="p-3.5 text-start">حساب المالك</th>
                            <th class="p-3.5 text-start">الرسوم / الحد الأدنى</th>
                            <th class="p-3.5 text-start">حالة التفعيل</th>
                            <th class="p-3.5 text-end">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sufra-border/60">
                        @foreach ($restaurants as $restaurant)
                            <tr class="hover:bg-sufra-bg/40 transition-colors">
                                {{-- اسم المطعم وشعاره --}}
                                <td class="p-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded border border-sufra-border overflow-hidden bg-sufra-bg flex-shrink-0">
                                            <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('restaurants.show', $restaurant->slug) }}" target="_blank" class="font-heading font-medium text-16 text-sufra-text hover:text-sufra-primary hover:underline truncate block">
                                                {{ $restaurant->name }}
                                            </a>
                                            <span class="text-14 text-sufra-muted block">
                                                تجهيز: {{ $restaurant->formatted_prep_time }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- المطبخ والحي --}}
                                <td class="p-3.5">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-badge variant="neutral">{{ $restaurant->cuisine }}</x-badge>
                                    </div>
                                    <span class="text-14 text-sufra-muted flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-sufra-primary"></i>
                                        <span>{{ $restaurant->district }}</span>
                                    </span>
                                </td>

                                {{-- المالك --}}
                                <td class="p-3.5">
                                    <strong class="text-sufra-text block font-medium">
                                        {{ $restaurant->user->name }}
                                    </strong>
                                    <span class="text-14 text-sufra-muted block">
                                        {{ $restaurant->user->email }}
                                    </span>
                                </td>

                                {{-- الرسوم والحد الأدنى --}}
                                <td class="p-3.5">
                                    <div class="text-14">
                                        <span class="text-sufra-muted">توصيل:</span>
                                        <span class="font-medium text-sufra-text">{{ $restaurant->formatted_delivery_fee }}</span>
                                    </div>
                                    <div class="text-14">
                                        <span class="text-sufra-muted">أدنى:</span>
                                        <span class="font-medium text-sufra-text">{{ $restaurant->formatted_min_order }}</span>
                                    </div>
                                </td>

                                {{-- زر التفعيل والتعطيل الفوري (المتطلب 3) --}}
                                <td class="p-3.5">
                                    <button
                                        type="button"
                                        wire:click="toggleActive({{ $restaurant->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded text-14 font-medium transition-colors border {{ $restaurant->is_active ? 'border-sufra-border bg-sufra-bg text-sufra-primary hover:bg-sufra-surface' : 'border-sufra-border bg-sufra-surface text-sufra-muted hover:bg-sufra-bg' }}"
                                        title="اضغط للتبديل الفوري"
                                    >
                                        <span class="w-2 h-2 rounded-full {{ $restaurant->is_active ? 'bg-sufra-primary' : 'bg-sufra-muted' }}"></span>
                                        <span>{{ $restaurant->is_active ? 'نشط بالمنصة' : 'معطّل' }}</span>
                                    </button>
                                </td>

                                {{-- زر التعديل --}}
                                <td class="p-3.5 text-end">
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $restaurant->id }})"
                                        class="p-2 rounded border border-sufra-border text-sufra-muted hover:text-sufra-text hover:bg-sufra-bg transition-colors"
                                        title="تعديل بيانات المطعم"
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
                {{ $restaurants->links() }}
            </div>
        </div>
    @endif

    {{-- نافذة إضافة مطعم جديد وإنشاء حساب لمالكه (المتطلب 3) --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-sufra-text/40 transition-opacity" wire:click="closeModal"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl bg-sufra-surface border border-sufra-border rounded shadow-none p-5 sm:p-6 my-8 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-sufra-border">
                        <div class="flex items-center gap-2">
                            <i data-lucide="store" class="w-5 h-5 text-sufra-primary"></i>
                            <h3 class="font-heading font-semibold text-16 text-sufra-text">إضافة مطعم جديد مع إنشاء حساب المالك</h3>
                        </div>
                        <button type="button" wire:click="closeModal" class="p-1 rounded text-sufra-muted hover:text-sufra-text">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="createRestaurant" class="space-y-5">
                        {{-- قسم بيانات المالك --}}
                        <div class="p-4 bg-sufra-bg/60 rounded border border-sufra-border space-y-3">
                            <h4 class="font-heading font-semibold text-14 text-sufra-text flex items-center gap-1.5">
                                <i data-lucide="user" class="w-4 h-4 text-sufra-primary"></i>
                                <span>بيانات حساب المالك (تسجيل الدخول للوحة المطعم)</span>
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="owner-name" value="اسم صاحب المطعم أو الممثل" />
                                    <x-text-input id="owner-name" type="text" wire:model.defer="ownerName" class="w-full mt-1" placeholder="مثال: فهد التميمي" />
                                    <x-input-error :messages="$errors->get('ownerName')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="owner-email" value="البريد الإلكتروني (لتسجيل الدخول)" />
                                    <x-text-input id="owner-email" type="email" wire:model.defer="ownerEmail" class="w-full mt-1" placeholder="owner@restaurant.test" />
                                    <x-input-error :messages="$errors->get('ownerEmail')" class="mt-1" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="owner-password" value="كلمة المرور الافتراضية" />
                                <x-text-input id="owner-password" type="text" wire:model.defer="ownerPassword" class="w-full mt-1" placeholder="كلمة مرور قوية (6 خانات على الأقل)" />
                                <x-input-error :messages="$errors->get('ownerPassword')" class="mt-1" />
                            </div>
                        </div>

                        {{-- قسم بيانات المطعم --}}
                        <div class="space-y-3">
                            <h4 class="font-heading font-semibold text-14 text-sufra-text flex items-center gap-1.5">
                                <i data-lucide="utensils" class="w-4 h-4 text-sufra-primary"></i>
                                <span>بيانات المطعم وقائمة التشغيل</span>
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="rest-name" value="اسم المطعم" />
                                    <x-text-input id="rest-name" type="text" wire:model.defer="name" class="w-full mt-1" placeholder="مثال: مطبخ القرية النجدية" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="rest-cuisine" value="نوع المطبخ (التصنيف)" />
                                    <x-text-input id="rest-cuisine" type="text" wire:model.defer="cuisine" class="w-full mt-1" placeholder="شعبي، كبسات، مشويات..." />
                                    <x-input-error :messages="$errors->get('cuisine')" class="mt-1" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <x-input-label for="rest-district" value="الحي" />
                                    <x-text-input id="rest-district" type="text" wire:model.defer="district" class="w-full mt-1" placeholder="مثال: النرجس" />
                                    <x-input-error :messages="$errors->get('district')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="rest-delivery-fee" value="رسوم التوصيل (ر.س)" />
                                    <x-text-input id="rest-delivery-fee" type="number" step="0.5" wire:model.defer="deliveryFee" class="w-full mt-1" />
                                    <x-input-error :messages="$errors->get('deliveryFee')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="rest-min-order" value="الحد الأدنى للطلب (ر.س)" />
                                    <x-text-input id="rest-min-order" type="number" step="1" wire:model.defer="minOrder" class="w-full mt-1" />
                                    <x-input-error :messages="$errors->get('minOrder')" class="mt-1" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="rest-prep-time" value="وقت التجهيز المتوقع (بالدقائق)" />
                                <x-text-input id="rest-prep-time" type="number" wire:model.defer="prepTime" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('prepTime')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="rest-desc" value="وصف المطعم (اختياري)" />
                                <textarea id="rest-desc" wire:model.defer="description" rows="2" class="w-full rounded border border-sufra-border bg-sufra-surface px-3 py-2 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:ring-0 transition-colors resize-none mt-1" placeholder="نبذة عن المطبخ وما يقدمه..."></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-sufra-border">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 rounded border border-sufra-border text-14 font-medium text-sufra-text bg-sufra-surface hover:bg-sufra-bg transition-colors">
                                إلغاء
                            </button>
                            <button type="submit" class="px-5 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none">
                                حفظ وإنشاء المطعم
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- نافذة تعديل مطعم قائم --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-sufra-text/40 transition-opacity" wire:click="closeModal"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-xl bg-sufra-surface border border-sufra-border rounded shadow-none p-5 sm:p-6 my-8 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-sufra-border">
                        <h3 class="font-heading font-semibold text-16 text-sufra-text">تعديل بيانات المطعم</h3>
                        <button type="button" wire:click="closeModal" class="p-1 rounded text-sufra-muted hover:text-sufra-text">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="updateRestaurant" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="edit-name" value="اسم المطعم" />
                                <x-text-input id="edit-name" type="text" wire:model.defer="name" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="edit-cuisine" value="نوع المطبخ" />
                                <x-text-input id="edit-cuisine" type="text" wire:model.defer="cuisine" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('cuisine')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <x-input-label for="edit-district" value="الحي" />
                                <x-text-input id="edit-district" type="text" wire:model.defer="district" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('district')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="edit-fee" value="رسوم التوصيل" />
                                <x-text-input id="edit-fee" type="number" step="0.5" wire:model.defer="deliveryFee" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('deliveryFee')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="edit-min" value="الحد الأدنى" />
                                <x-text-input id="edit-min" type="number" step="1" wire:model.defer="minOrder" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('minOrder')" class="mt-1" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="edit-prep" value="وقت التجهيز المتوقع (بالدقائق)" />
                            <x-text-input id="edit-prep" type="number" wire:model.defer="prepTime" class="w-full mt-1" />
                            <x-input-error :messages="$errors->get('prepTime')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="edit-desc" value="الوصف" />
                            <textarea id="edit-desc" wire:model.defer="description" rows="2" class="w-full rounded border border-sufra-border bg-sufra-surface px-3 py-2 text-14 text-sufra-text placeholder-sufra-muted focus:border-sufra-primary focus:ring-0 transition-colors resize-none mt-1"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-sufra-border">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 rounded border border-sufra-border text-14 font-medium text-sufra-text bg-sufra-surface hover:bg-sufra-bg transition-colors">
                                إلغاء
                            </button>
                            <button type="submit" class="px-5 py-2 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none">
                                حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

