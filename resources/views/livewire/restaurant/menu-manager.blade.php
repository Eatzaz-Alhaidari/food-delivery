<div>
    {{-- رأس الصفحة وأزرار الإجراءات السريعة --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-heading font-semibold text-20 text-sufra-text">قائمة الطعام (المنيو)</h1>
                <p class="text-14 text-sufra-muted mt-1">إدارة وتعديل التصنيفات، أصناف الطعام، والخيارات الإضافية.</p>
            </div>

            <div class="flex items-center gap-2">
                <x-button wire:click="openNewCategoryModal" variant="secondary" icon="folder-plus" size="md">
                    إضافة تصنيف
                </x-button>
                <x-button wire:click="openNewItemModal" variant="primary" icon="plus" size="md">
                    إضافة صنف
                </x-button>
            </div>
        </div>

        {{-- تنبيه النجاح الهادئ --}}
        @if ($successMessage)
            <div class="mt-4 p-3.5 bg-sufra-bg border border-sufra-primary rounded text-14 text-sufra-primary flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>{{ $successMessage }}</span>
                </div>
                <button type="button" wire:click="$set('successMessage', null)" class="text-sufra-muted hover:text-sufra-text">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
    </div>

    {{-- شريط التصنيفات وفلترة الأصناف --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-4 mb-6">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-sufra-border">
            <span class="text-14 font-medium text-sufra-text flex items-center gap-1.5">
                <i data-lucide="layers" class="w-4 h-4 text-sufra-primary"></i>
                <span>أقسام المنيو ({{ $categories->count() }})</span>
            </span>
            <span class="text-14 text-sufra-muted">انقر على القسم لفلترة الأصناف أو لتعديله</span>
        </div>

        @if ($categories->isEmpty())
            <div class="py-4 text-center text-14 text-sufra-muted">
                لا توجد تصنيفات حالياً. أضف تصنيفاً أولاً لترتيب أصنافك.
            </div>
        @else
            <div class="flex flex-wrap items-center gap-2">
                {{-- خيار عرض الكل --}}
                <button
                    type="button"
                    wire:click="$set('selectedCategoryId', 'all')"
                    class="px-3 py-1.5 rounded text-14 font-medium border transition-colors duration-150 {{ $selectedCategoryId === 'all' ? 'bg-sufra-primary text-sufra-surface border-sufra-primary' : 'bg-sufra-bg text-sufra-text border-sufra-border hover:bg-sufra-surface' }}"
                >
                    الكل ({{ $restaurant->menuItems()->count() }})
                </button>

                {{-- الأقسام مع أزرار التعديل والترتيب السريع --}}
                @foreach ($categories as $cat)
                    <div class="inline-flex items-center rounded border transition-colors duration-150 {{ $selectedCategoryId == $cat->id ? 'border-sufra-primary bg-sufra-surface' : 'border-sufra-border bg-sufra-bg' }}">
                        <button
                            type="button"
                            wire:click="$set('selectedCategoryId', {{ $cat->id }})"
                            class="px-3 py-1.5 text-14 font-medium text-sufra-text hover:text-sufra-primary"
                        >
                            {{ $cat->name }}
                            <span class="text-14 text-sufra-muted ms-1">({{ $cat->menu_items_count }})</span>
                        </button>
                        
                        {{-- أدوات التصنيف السريعة (تعديل، حذف، ترتيب) --}}
                        <div class="flex items-center pe-1.5 border-s border-sufra-border gap-0.5">
                            <button type="button" wire:click="moveCategoryUp({{ $cat->id }})" title="تقديم" class="p-1 text-sufra-muted hover:text-sufra-text">
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </button>
                            <button type="button" wire:click="moveCategoryDown({{ $cat->id }})" title="تأخير" class="p-1 text-sufra-muted hover:text-sufra-text">
                                <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                            </button>
                            <button type="button" wire:click="editCategory({{ $cat->id }})" title="تعديل" class="p-1 text-sufra-muted hover:text-sufra-primary">
                                <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                            </button>
                            <button type="button" wire:click="deleteCategory({{ $cat->id }})" wire:confirm="هل أنت متأكد من حذف هذا التصنيف وجميع الأصناف التابعة له؟" title="حذف" class="p-1 text-sufra-muted hover:text-sufra-accent">
                                <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- قائمة الأصناف: تُعرض كصفوف أفقية مريحة للعين وفق قاعدة DESIGN.md --}}
    <div>
        @if ($items->isEmpty())
            <x-empty-state
                title="لا توجد أصناف في هذا القسم"
                description="ابدأ بإضافة أول صنف في قائمتك مع السعر والصورة والإضافات."
                icon="utensils"
            >
                <x-slot:action>
                    <x-button wire:click="openNewItemModal" variant="primary" icon="plus">
                        إضافة صنف جديد
                    </x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="space-y-3">
                @foreach ($items as $item)
                    <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 transition-colors hover:border-sufra-primary/50 {{ ! $item->is_available ? 'opacity-65' : '' }}">
                        {{-- الجزء الأيمن: صورة الصنف + البيانات --}}
                        <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                            {{-- صورة الصنف المصغرة --}}
                            <div class="w-20 h-20 sm:w-24 sm:h-20 rounded border border-sufra-border overflow-hidden bg-sufra-bg flex-shrink-0">
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            </div>

                            {{-- تفاصيل الصنف --}}
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-heading font-semibold text-16 text-sufra-text truncate">
                                        {{ $item->name }}
                                    </span>
                                    <span class="text-14 px-2 py-0.5 rounded border border-sufra-border text-sufra-muted">
                                        {{ $item->category->name }}
                                    </span>
                                </div>

                                @if ($item->description)
                                    <p class="text-14 text-sufra-muted line-clamp-1 mb-1.5">
                                        {{ $item->description }}
                                    </p>
                                @endif

                                <div class="flex items-center gap-3 text-14">
                                    <span class="font-semibold text-sufra-primary">
                                        {{ $item->formatted_price }}
                                    </span>

                                    @if ($item->addons->isNotEmpty())
                                        <span class="text-sufra-muted flex items-center gap-1">
                                            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                            <span>{{ $item->addons->count() }} إضافات</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- الجزء الأيسر: زر التوفر + أزرار التعديل والحذف --}}
                        <div class="flex items-center justify-between sm:justify-end gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-sufra-border">
                            {{-- زر التوفر اللحظي بدون إعادة تحميل --}}
                            <button
                                type="button"
                                wire:click="toggleAvailability({{ $item->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border text-14 font-medium transition-colors cursor-pointer select-none {{ $item->is_available ? 'bg-sufra-bg border-sufra-primary text-sufra-primary hover:bg-sufra-surface' : 'bg-sufra-bg border-sufra-accent text-sufra-accent hover:bg-sufra-surface' }}"
                            >
                                <span class="w-2 h-2 rounded-full {{ $item->is_available ? 'bg-sufra-primary' : 'bg-sufra-accent' }}"></span>
                                <span>{{ $item->is_available ? 'متوفر للطلب' : 'نفد مؤقتاً' }}</span>
                            </button>

                            {{-- أزرار الإجراءات --}}
                            <div class="flex items-center gap-1.5">
                                <x-button wire:click="editItem({{ $item->id }})" variant="secondary" size="sm" icon="edit-2">
                                    تعديل
                                </x-button>
                                <button
                                    type="button"
                                    wire:click="deleteItem({{ $item->id }})"
                                    wire:confirm="هل أنت متأكد من حذف الصنف «{{ $item->name }}» نهائياً؟"
                                    class="p-2 rounded border border-sufra-border text-sufra-muted hover:text-sufra-accent hover:border-sufra-accent transition-colors"
                                    title="حذف الصنف"
                                >
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- نافذة منبثقة (Modal) لإضافة/تعديل التصنيف --}}
    @if ($showCategoryModal)
        <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
            <div class="bg-sufra-surface border border-sufra-border rounded w-full max-w-md p-6" @click.outside="$set('showCategoryModal', false)">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-sufra-border">
                    <h2 class="font-heading font-semibold text-16 text-sufra-text">
                        {{ $editingCategoryId ? 'تعديل التصنيف' : 'إضافة تصنيف جديد' }}
                    </h2>
                    <button type="button" wire:click="$set('showCategoryModal', false)" class="text-sufra-muted hover:text-sufra-text">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form wire:submit="saveCategory" class="space-y-4">
                    <x-input wire:model="categoryName" label="اسم التصنيف" placeholder="مثال: أطباق رئيسية، مقبلات، حلويات" required :error="$errors->first('categoryName')" />
                    <x-input wire:model="categorySortOrder" type="number" label="ترتيب العرض (الرقم الأصغر يظهر أولاً)" required :error="$errors->first('categorySortOrder')" />

                    <div class="pt-4 border-t border-sufra-border flex justify-end gap-2">
                        <x-button type="button" wire:click="$set('showCategoryModal', false)" variant="secondary">
                            إلغاء
                        </x-button>
                        <x-button type="submit" variant="primary">
                            حفظ التصنيف
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- نافذة منبثقة (Modal) لإضافة/تعديل الصنف مع إدارة الإضافات --}}
    @if ($showItemModal)
        <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-sufra-surface border border-sufra-border rounded w-full max-w-2xl p-6 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-sufra-border">
                    <h2 class="font-heading font-semibold text-16 text-sufra-text">
                        {{ $editingItemId ? 'تعديل الصنف' : 'إضافة صنف جديد' }}
                    </h2>
                    <button type="button" wire:click="$set('showItemModal', false)" class="text-sufra-muted hover:text-sufra-text">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form wire:submit="saveItem" class="space-y-4">
                    {{-- اختيار التصنيف --}}
                    <div>
                        <label for="itemCategoryId" class="block font-medium text-14 text-sufra-text mb-1.5">
                            التصنيف التابع له الصنف <span class="text-sufra-accent">*</span>
                        </label>
                        <select
                            wire:model="itemCategoryId"
                            id="itemCategoryId"
                            class="w-full bg-sufra-surface border border-sufra-border text-sufra-text text-16 rounded px-3.5 py-2.5 focus:border-sufra-primary focus:ring-1 focus:ring-sufra-primary outline-none"
                        >
                            <option value="">-- اختر التصنيف --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('itemCategoryId')
                            <p class="mt-1.5 text-14 text-sufra-accent flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    {{-- اسم الصنف والسعر --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <x-input wire:model="itemName" label="اسم الصنف" placeholder="مثال: كبسة حاشي، مندي لحم" required :error="$errors->first('itemName')" />
                        </div>
                        <div>
                            <x-input wire:model="itemPrice" type="number" step="0.5" label="السعر (ر.س)" placeholder="0.00" required :error="$errors->first('itemPrice')" />
                        </div>
                    </div>

                    {{-- الوصف --}}
                    <div>
                        <label for="itemDescription" class="block font-medium text-14 text-sufra-text mb-1.5">
                            وصف الصنف والمكونات
                        </label>
                        <textarea
                            wire:model="itemDescription"
                            id="itemDescription"
                            rows="2"
                            class="w-full bg-sufra-surface border border-sufra-border text-sufra-text text-16 rounded px-3.5 py-2.5 focus:border-sufra-primary focus:ring-1 focus:ring-sufra-primary outline-none resize-none"
                            placeholder="وصف مختصر لمكونات الطبق وطريقة تحضيره..."
                        ></textarea>
                        @error('itemDescription')
                            <p class="mt-1.5 text-14 text-sufra-accent">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- صورة الصنف وحالة التوفر --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                        <div>
                            <label class="block font-medium text-14 text-sufra-text mb-1.5">
                                صورة الصنف
                            </label>
                            <input type="file" wire:model="itemImage" accept="image/*" class="text-14 text-sufra-muted file:ms-0 file:me-3 file:py-1 file:px-2.5 file:rounded file:border file:border-sufra-border file:text-14 file:font-medium file:bg-sufra-bg file:text-sufra-text hover:file:bg-sufra-surface cursor-pointer">
                            <p class="text-14 text-sufra-muted mt-1">حجم أقصى 2MB (JPG, PNG, WEBP)</p>
                            @error('itemImage')
                                <p class="text-14 text-sufra-accent mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-14 text-sufra-text mb-1.5">
                                حالة التوفر الفورية
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" wire:model="itemIsAvailable" class="w-4 h-4 rounded border-sufra-border text-sufra-primary focus:ring-sufra-primary bg-sufra-surface">
                                <span class="text-14 text-sufra-text">متوفر للطلب الآن</span>
                            </label>
                        </div>
                    </div>

                    {{-- قسم إدارة الإضافات التابعة للصنف (المتطلب 6) --}}
                    <div class="pt-4 border-t border-sufra-border">
                        <h3 class="font-heading font-semibold text-16 text-sufra-text mb-2 flex items-center gap-1.5">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-sufra-primary"></i>
                            <span>إضافات وخيارات الصنف (اختيارية للعميل)</span>
                        </h3>
                        <p class="text-14 text-sufra-muted mb-3">يمكنك تحديد خيارات بسعر إضافي أو مجاني (مثال: رز زيادة، طحينة، مكسرات).</p>

                        {{-- نموذج إضافة خيار جديد --}}
                        <div class="bg-sufra-bg border border-sufra-border rounded p-3 mb-3">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                <div class="sm:col-span-2">
                                    <label class="block font-medium text-14 text-sufra-text mb-1">اسم الإضافة</label>
                                    <input
                                        type="text"
                                        wire:model="newAddonName"
                                        placeholder="مثال: طحينة إضافية، رز بشاور إضافي"
                                        class="w-full bg-sufra-surface border border-sufra-border text-sufra-text text-14 rounded px-3 py-1.5 outline-none focus:border-sufra-primary"
                                    >
                                </div>
                                <div>
                                    <label class="block font-medium text-14 text-sufra-text mb-1">السعر الإضافي (ر.س)</label>
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="number"
                                            step="0.5"
                                            wire:model="newAddonPrice"
                                            placeholder="0.00"
                                            class="w-full bg-sufra-surface border border-sufra-border text-sufra-text text-14 rounded px-3 py-1.5 outline-none focus:border-sufra-primary"
                                        >
                                        <x-button type="button" wire:click="addAddon" variant="secondary" size="sm" class="flex-shrink-0">
                                            إدراج
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                            @error('newAddonName')
                                <p class="text-14 text-sufra-accent mt-1">{{ $message }}</p>
                            @enderror
                            @error('newAddonPrice')
                                <p class="text-14 text-sufra-accent mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- جدول أو قائمة الإضافات المضافة حالياً --}}
                        @if (!empty($itemAddons))
                            <div class="space-y-1.5 border border-sufra-border rounded p-2 bg-sufra-surface">
                                @foreach ($itemAddons as $index => $addon)
                                    <div class="flex items-center justify-between p-2 rounded bg-sufra-bg text-14">
                                        <span class="font-medium text-sufra-text">{{ $addon['name'] }}</span>
                                        <div class="flex items-center gap-3">
                                            <span class="text-sufra-primary font-semibold">
                                                {{ $addon['extra_price'] > 0 ? '+ ' . number_format($addon['extra_price'], 0) . ' ر.س' : 'مجاناً' }}
                                            </span>
                                            <button type="button" wire:click="removeAddon({{ $index }})" class="text-sufra-muted hover:text-sufra-accent">
                                                <i data-lucide="trash" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- أزرار الحفظ والإلغاء --}}
                    <div class="pt-4 border-t border-sufra-border flex justify-end gap-2">
                        <x-button type="button" wire:click="$set('showItemModal', false)" variant="secondary">
                            إلغاء
                        </x-button>
                        <x-button type="submit" variant="primary">
                            حفظ الصنف
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

