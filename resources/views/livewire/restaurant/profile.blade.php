<div>
    {{-- عنوان الصفحة وزر تبديل حالة الفتح/الإغلاق اللحظي --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-heading font-semibold text-20 text-sufra-text">بيانات المطعم</h1>
                <p class="text-14 text-sufra-muted mt-1">تحديث الهوية، رسوم التوصيل، وحالة استقبال الطلبات.</p>
            </div>

            {{-- زر الفتح والإغلاق الفوري بدون إعادة تحميل --}}
            <div>
                @if ($is_open)
                    <x-button wire:click="toggleOpen" variant="primary" icon="check-circle" size="md">
                        المطعم مفتوح الآن
                    </x-button>
                @else
                    <x-button wire:click="toggleOpen" variant="secondary" icon="clock" size="md" class="text-sufra-accent border-sufra-accent">
                        المطعم مغلق حالياً
                    </x-button>
                @endif
            </div>
        </div>

        {{-- تنبيه رسالة النجاح الهادئ --}}
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

    {{-- استمارة تعديل بيانات المطعم --}}
    <form wire:submit="save" class="bg-sufra-surface border border-sufra-border rounded p-6 space-y-6">
        {{-- اسم المطعم --}}
        <x-input wire:model="name" label="اسم المطعم" id="name" required :error="$errors->first('name')" />

        {{-- وصف المطعم --}}
        <div>
            <label for="description" class="block font-medium text-14 text-sufra-text mb-1.5">
                وصف المطعم (نبذة قصيرة)
            </label>
            <textarea
                wire:model="description"
                id="description"
                rows="3"
                class="w-full bg-sufra-surface border border-sufra-border text-sufra-text text-16 rounded px-3.5 py-2.5 focus:border-sufra-primary focus:ring-1 focus:ring-sufra-primary outline-none resize-none"
                placeholder="أشهى الأطباق الشعبية والمشويات بنكهات نجدية أصيلة..."
            ></textarea>
            @error('description')
                <p class="mt-1.5 text-14 text-sufra-accent flex items-center gap-1.5">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- الحي ونوع المطبخ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input wire:model="cuisine" label="نوع المطبخ (التصنيف العام)" id="cuisine" placeholder="مثال: شعبي، مندي، مشويات" required :error="$errors->first('cuisine')" />
            <x-input wire:model="district" label="الحي (في الرياض)" id="district" placeholder="مثال: الصحافة، العليا، الملز" required :error="$errors->first('district')" />
        </div>

        {{-- الأسعار والأوقات --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-input wire:model="min_order" type="number" step="0.5" label="الحد الأدنى للطلب (ر.س)" id="min_order" required :error="$errors->first('min_order')" />
            <x-input wire:model="delivery_fee" type="number" step="0.5" label="رسوم التوصيل (ر.س)" id="delivery_fee" required :error="$errors->first('delivery_fee')" />
            <x-input wire:model="prep_time" type="number" label="وقت التجهيز (بالدقائق)" id="prep_time" required :error="$errors->first('prep_time')" />
        </div>

        {{-- رفع الشعار وصورة الغلاف --}}
        <div class="pt-4 border-t border-sufra-border grid grid-cols-1 sm:grid-cols-2 gap-6">
            {{-- الشعار --}}
            <div>
                <label class="block font-medium text-14 text-sufra-text mb-2">
                    شعار المطعم (Logo)
                </label>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded border border-sufra-border overflow-hidden bg-sufra-bg flex-shrink-0 flex items-center justify-center">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" alt="معاينة الشعار" class="w-full h-full object-cover">
                        @else
                            <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" wire:model="logo" accept="image/*" class="text-14 text-sufra-muted file:ms-0 file:me-3 file:py-1.5 file:px-3 file:rounded file:border file:border-sufra-border file:text-14 file:font-medium file:bg-sufra-bg file:text-sufra-text hover:file:bg-sufra-surface cursor-pointer">
                        <p class="text-14 text-sufra-muted mt-1">حجم أقصى 2MB (JPG, PNG, WEBP)</p>
                        @error('logo')
                            <p class="text-14 text-sufra-accent mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- الغلاف --}}
            <div>
                <label class="block font-medium text-14 text-sufra-text mb-2">
                    صورة الغلاف (Cover)
                </label>
                <div class="flex items-center gap-4">
                    <div class="w-28 h-20 rounded border border-sufra-border overflow-hidden bg-sufra-bg flex-shrink-0 flex items-center justify-center">
                        @if ($cover)
                            <img src="{{ $cover->temporaryUrl() }}" alt="معاينة الغلاف" class="w-full h-full object-cover">
                        @else
                            <img src="{{ $restaurant->cover_url }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" wire:model="cover" accept="image/*" class="text-14 text-sufra-muted file:ms-0 file:me-3 file:py-1.5 file:px-3 file:rounded file:border file:border-sufra-border file:text-14 file:font-medium file:bg-sufra-bg file:text-sufra-text hover:file:bg-sufra-surface cursor-pointer">
                        <p class="text-14 text-sufra-muted mt-1">حجم أقصى 3MB (JPG, PNG, WEBP)</p>
                        @error('cover')
                            <p class="text-14 text-sufra-accent mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- زر الحفظ --}}
        <div class="pt-4 border-t border-sufra-border flex justify-end">
            <x-button type="submit" variant="primary" icon="save" size="md">
                حفظ التعديلات
            </x-button>
        </div>
    </form>
</div>

