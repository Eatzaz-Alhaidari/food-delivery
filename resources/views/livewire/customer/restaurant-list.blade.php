<div>
    {{-- عنوان الصفحة وقسم البحث والفلاتر --}}
    <div class="mb-6 space-y-4">
        <div>
            <h1 class="font-heading font-semibold text-28 text-sufra-text">استكشف المطابخ والمطاعم</h1>
            <p class="text-14 text-sufra-muted mt-1">تصفح المطاعم المحلية في الرياض واطلب وجبتك المفضلة.</p>
        </div>

        {{-- شريط البحث الرئيسي --}}
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-sufra-muted">
                <i data-lucide="search" class="w-5 h-5"></i>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="ابحث بالاسم أو الحي (مثال: شواية نجد، الصحافة، مندي...)"
                class="w-full bg-sufra-surface border border-sufra-border text-sufra-text text-16 rounded ps-11 pe-10 py-3 focus:border-sufra-primary focus:ring-1 focus:ring-sufra-primary outline-none shadow-none"
            >
            @if (!empty($search))
                <button
                    type="button"
                    wire:click="$set('search', '')"
                    class="absolute inset-y-0 end-0 flex items-center pe-3.5 text-sufra-muted hover:text-sufra-text"
                >
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            @endif
        </div>

        {{-- شريط تصنيفات المطابخ (Pills) --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 pt-1 scrollbar-none">
            <button
                type="button"
                wire:click="$set('cuisine', '')"
                class="px-3.5 py-1.5 rounded text-14 font-medium border transition-colors duration-150 flex-shrink-0 cursor-pointer {{ empty($cuisine) ? 'bg-sufra-primary text-sufra-surface border-sufra-primary' : 'bg-sufra-surface text-sufra-text border-sufra-border hover:bg-sufra-bg' }}"
            >
                جميع المطابخ
            </button>

            @foreach ($availableCuisines as $c)
                <button
                    type="button"
                    wire:click="selectCuisine('{{ $c }}')"
                    class="px-3.5 py-1.5 rounded text-14 font-medium border transition-colors duration-150 flex-shrink-0 cursor-pointer {{ $cuisine === $c ? 'bg-sufra-primary text-sufra-surface border-sufra-primary' : 'bg-sufra-surface text-sufra-text border-sufra-border hover:bg-sufra-bg' }}"
                >
                    {{ $c }}
                </button>
            @endforeach
        </div>

        {{-- خيارات الفلترة المتقدمة والترتيب --}}
        <div class="bg-sufra-surface border border-sufra-border rounded p-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-14">
            <div class="flex items-center gap-4">
                {{-- خيار مفتوح الآن فقط --}}
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        wire:model.live="openOnly"
                        class="w-4 h-4 rounded border-sufra-border text-sufra-primary focus:ring-sufra-primary bg-sufra-surface focus:ring-offset-0"
                    >
                    <span class="text-sufra-text font-medium">مفتوح الآن فقط</span>
                </label>

                {{-- عدد النتائج --}}
                <span class="text-sufra-muted border-s border-sufra-border ps-3">
                    {{ $restaurants->count() }} مطعم متاح
                </span>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-sufra-muted text-14">الترتيب:</span>
                <select
                    wire:model.live="sortBy"
                    class="bg-sufra-surface border border-sufra-border text-sufra-text text-14 rounded px-3 py-1.5 focus:border-sufra-primary focus:ring-1 focus:ring-sufra-primary outline-none"
                >
                    <option value="">الافتراضي (المفتوح أولاً)</option>
                    <option value="fastest">الأسرع تحضيراً</option>
                    <option value="lowest_fee">الأقل رسوم توصيل</option>
                </select>

                @if (!empty($search) || !empty($cuisine) || $openOnly || !empty($sortBy))
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="text-14 text-sufra-accent hover:underline ms-2 flex items-center gap-1"
                    >
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        <span>إعادة ضبط</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- قائمة المطاعم كصفوف أفقية مريحة للعين (حسب قاعدة DESIGN.md الصارمة) --}}
    <div>
        @if ($restaurants->isEmpty())
            <x-empty-state
                title="لم نجد أي مطعم يطابق بحثك"
                description="جرّب تعديل كلمات البحث، أو إلغاء تفعيل الفلاتر لعرض كافة المطاعم المسجلة."
                icon="search"
            >
                <x-slot:action>
                    <x-button wire:click="resetFilters" variant="secondary" icon="rotate-ccw">
                        عرض جميع المطاعم
                    </x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="space-y-3">
                @foreach ($restaurants as $restaurant)
                    <a
                        href="{{ route('restaurants.show', $restaurant->slug) }}"
                        class="block bg-sufra-surface border border-sufra-border rounded p-4 hover:border-sufra-primary transition-all duration-150 {{ ! $restaurant->is_open ? 'opacity-75' : '' }}"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            {{-- الجزء الأيمن: الشعار والمعلومات الرئيسية --}}
                            <div class="flex items-start sm:items-center gap-4 min-w-0">
                                {{-- شعار المطعم المصغر --}}
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded border border-sufra-border overflow-hidden bg-sufra-bg flex-shrink-0">
                                    <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                                </div>

                                {{-- التفاصيل --}}
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <h2 class="font-heading font-semibold text-16 sm:text-20 text-sufra-text">
                                            {{ $restaurant->name }}
                                        </h2>
                                        <x-badge variant="neutral">
                                            {{ $restaurant->cuisine }}
                                        </x-badge>
                                        <span class="inline-flex items-center gap-1 text-14 text-sufra-primary font-medium bg-sufra-bg px-2 py-0.5 rounded border border-sufra-border">
                                            <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                            <span>{{ $restaurant->average_rating }}</span>
                                            <span class="text-sufra-muted text-[12px]">({{ $restaurant->reviews_count }})</span>
                                        </span>
                                        @if ($restaurant->is_open)
                                            <x-badge variant="primary" icon="check-circle">مفتوح</x-badge>
                                        @else
                                            <x-badge variant="neutral" icon="clock">مغلق</x-badge>
                                        @endif
                                    </div>

                                    @if ($restaurant->description)
                                        <p class="text-14 text-sufra-muted line-clamp-1 mb-2">
                                            {{ $restaurant->description }}
                                        </p>
                                    @endif

                                    {{-- بيانات الحي وأوقات التحضير والرسوم --}}
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-14 text-sufra-muted">
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-sufra-primary"></i>
                                            <span>{{ $restaurant->district }}</span>
                                        </span>
                                        <span>&bull;</span>
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3.5 h-3.5 text-sufra-primary"></i>
                                            <span>{{ $restaurant->formatted_prep_time }}</span>
                                        </span>
                                        <span>&bull;</span>
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="truck" class="w-3.5 h-3.5 text-sufra-primary"></i>
                                            <span>التوصيل: {{ $restaurant->formatted_delivery_fee }}</span>
                                        </span>
                                        <span>&bull;</span>
                                        <span>الحد الأدنى: {{ $restaurant->formatted_min_order }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- الجزء الأيسر: زر الدخول للمنيو --}}
                            <div class="flex items-center justify-end pt-2 sm:pt-0 border-t sm:border-t-0 border-sufra-border">
                                <span class="text-14 font-medium text-sufra-primary flex items-center gap-1 group-hover:translate-x-[-2px] transition-transform">
                                    <span>عرض المنيو</span>
                                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

