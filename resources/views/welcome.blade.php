<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'سُفرة') }} — مطابخ ومطاعم سعودية محلية</title>

        <!-- Google Fonts: IBM Plex Sans Arabic & Noto Kufi Arabic -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Noto+Kufi+Arabic:wght@600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-sufra-bg text-sufra-text font-sans min-h-screen antialiased flex flex-col">
        {{-- شريط الإشعار العلوي الهادئ بنمط الورق --}}
        <div class="bg-sufra-primary text-sufra-surface text-14 py-2 px-4 text-center border-b border-sufra-border/40">
            <div class="max-w-sufra mx-auto flex items-center justify-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1 font-medium">
                    <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                    <span>خصم 10% على أول طلب لك باستخدام كود الكوبون:</span>
                </span>
                <span class="bg-sufra-surface text-sufra-primary px-2 py-0.5 rounded font-heading font-semibold text-14 tracking-wider">
                    SUFRA10
                </span>
                <span class="text-sufra-surface/80 text-[13px] hidden sm:inline">&bull; توصيل سريع من أشهى المطابخ المحلية</span>
            </div>
        </div>

        {{-- شريط التنقل العلوي --}}
        <header class="bg-sufra-surface border-b border-sufra-border sticky top-0 z-30 shadow-none">
            <div class="max-w-sufra mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <a href="/" class="flex items-center gap-2">
                        <x-application-logo />
                    </a>
                    <nav class="hidden sm:flex items-center gap-5 text-14">
                        <a href="{{ route('restaurants.index') }}" class="font-medium text-sufra-text hover:text-sufra-primary transition-colors flex items-center gap-1.5">
                            <i data-lucide="store" class="w-4 h-4 text-sufra-muted"></i>
                            <span>استكشف المطاعم</span>
                        </a>
                        <a href="#cuisines" class="text-sufra-muted hover:text-sufra-text transition-colors">
                            أشهر المطابخ
                        </a>
                        <a href="#how-it-works" class="text-sufra-muted hover:text-sufra-text transition-colors">
                            طريقة الطلب
                        </a>
                    </nav>
                </div>

                <div class="flex items-center gap-3">
                    {{-- أيقونة السلة المتزامنة --}}
                    <livewire:customer.cart-icon />

                    @auth
                        <div class="flex items-center gap-3">
                            <span class="text-14 text-sufra-muted hidden sm:inline">
                                مرحباً، <strong class="text-sufra-text">{{ Auth::user()->name }}</strong>
                            </span>
                            
                            @if(Auth::user()->isAdmin())
                                <x-badge variant="solid-olive">مدير</x-badge>
                                <x-button href="/admin" variant="secondary" size="sm">لوحة الإدارة</x-button>
                            @elseif(Auth::user()->isRestaurant())
                                <x-badge variant="solid-olive">مطعم</x-badge>
                                <x-button href="/restaurant" variant="secondary" size="sm">لوحة المطعم</x-button>
                            @else
                                <a href="{{ route('orders.index') }}" class="hidden sm:inline-flex items-center gap-1 text-14 text-sufra-text hover:text-sufra-primary font-medium">
                                    <i data-lucide="shopping-bag" class="w-4 h-4 text-sufra-muted"></i>
                                    <span>طلباتي</span>
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <x-button type="submit" variant="secondary" size="sm" icon="log-out">
                                    خروج
                                </x-button>
                            </form>
                        </div>
                    @else
                        <x-button href="{{ route('login') }}" variant="secondary" size="sm">
                            تسجيل الدخول
                        </x-button>
                        <x-button href="{{ route('register') }}" variant="primary" size="sm">
                            إنشاء حساب
                        </x-button>
                    @endauth
                </div>
            </div>
        </header>

        {{-- المحتوى الرئيسي --}}
        <main class="flex-1 max-w-sufra w-full mx-auto px-4 sm:px-6 py-6 sm:py-10 space-y-12">
            
            {{-- ١. القسم الترحيبي الرئيسي (Hero Section) بنمط بطاقة ورقية فخمة --}}
            <section class="bg-sufra-surface border border-sufra-border rounded p-6 sm:p-10 relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    
                    {{-- الجانب الأيمن: العناوين ومربع البحث --}}
                    <div class="lg:col-span-7 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded border border-sufra-border bg-sufra-bg text-14 font-medium text-sufra-primary">
                            <i data-lucide="utensils" class="w-4 h-4"></i>
                            <span>أطباق شعبية ومحلية تطبخ طازجة عند الطلب</span>
                        </div>

                        <h1 class="font-heading font-semibold text-28 sm:text-40 text-sufra-text leading-tight">
                            أشهى أطباق المطابخ المحلية إلى سفرتك مباشرة
                        </h1>

                        <p class="text-14 sm:text-16 text-sufra-muted leading-relaxed max-w-xl">
                            من كبسة الحاشي ومندي التراث إلى دجاج الشواية البشاوري ومشاوي الخالدية، تصفح المنيو واطلب مباشرة بأسعار المطعم الحقيقية دون أي رسوم خفية.
                        </p>

                        {{-- مربع البحث المباشر السريع --}}
                        <form action="{{ route('restaurants.index') }}" method="GET" class="pt-2 max-w-xl">
                            <div class="relative flex items-center">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-sufra-muted">
                                    <i data-lucide="search" class="w-5 h-5"></i>
                                </div>
                                <input
                                    type="text"
                                    name="q"
                                    placeholder="ابحث عن مطبخ، طبق، أو حي (مثال: كبسة، حاشي، الصحافة...)"
                                    class="w-full bg-sufra-bg border border-sufra-border text-sufra-text text-14 sm:text-16 rounded ps-11 pe-24 py-3 focus:border-sufra-primary focus:ring-0 outline-none transition-colors"
                                >
                                <div class="absolute inset-y-0 end-1.5 flex items-center">
                                    <x-button type="submit" variant="primary" size="sm">
                                        ابحث الآن
                                    </x-button>
                                </div>
                            </div>
                        </form>

                        {{-- وسوم التصفية السريعة --}}
                        <div class="pt-2 flex flex-wrap items-center gap-2 text-14">
                            <span class="text-sufra-muted text-14">أشهر المطابخ:</span>
                            <a href="{{ route('restaurants.index', ['cuisine' => 'شعبي']) }}" class="px-2.5 py-1 rounded border border-sufra-border bg-sufra-bg text-sufra-text hover:border-sufra-primary hover:text-sufra-primary transition-colors">
                                شعبي
                            </a>
                            <a href="{{ route('restaurants.index', ['cuisine' => 'لحوم حاشي']) }}" class="px-2.5 py-1 rounded border border-sufra-border bg-sufra-bg text-sufra-text hover:border-sufra-primary hover:text-sufra-primary transition-colors">
                                لحوم حاشي
                            </a>
                            <a href="{{ route('restaurants.index', ['cuisine' => 'بخاري']) }}" class="px-2.5 py-1 rounded border border-sufra-border bg-sufra-bg text-sufra-text hover:border-sufra-primary hover:text-sufra-primary transition-colors">
                                بخاري وشواية
                            </a>
                            <a href="{{ route('restaurants.index', ['cuisine' => 'مشاوي']) }}" class="px-2.5 py-1 rounded border border-sufra-border bg-sufra-bg text-sufra-text hover:border-sufra-primary hover:text-sufra-primary transition-colors">
                                مشاوي
                            </a>
                            <a href="{{ route('restaurants.index', ['cuisine' => 'شاورما']) }}" class="px-2.5 py-1 rounded border border-sufra-border bg-sufra-bg text-sufra-text hover:border-sufra-primary hover:text-sufra-primary transition-colors">
                                شاورما وفطائر
                            </a>
                        </div>
                    </div>

                    {{-- الجانب الأيسر: بطاقة طبق اليوم المميز --}}
                    <div class="lg:col-span-5">
                        <div class="bg-sufra-bg border border-sufra-border rounded p-3 sm:p-4 relative">
                            {{-- صورة الطبق المميز --}}
                            <div class="h-56 sm:h-64 w-full rounded border border-sufra-border overflow-hidden relative">
                                <img
                                    src="/storage/demo/dishes/kabsa-hashi-wild.jpg"
                                    alt="كبسة حاشي برية"
                                    class="w-full h-full object-cover"
                                >
                                {{-- شارة الطبق الأكثر طلباً --}}
                                <div class="absolute top-3 end-3 bg-sufra-surface/95 border border-sufra-border px-2.5 py-1 rounded text-14 font-medium text-sufra-primary flex items-center gap-1">
                                    <i data-lucide="award" class="w-4 h-4 text-sufra-accent"></i>
                                    <span>الأكثر طلباً اليوم</span>
                                </div>
                            </div>

                            {{-- معلومات الطبق في البطاقة --}}
                            <div class="pt-3.5 space-y-1.5">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-heading font-semibold text-16 sm:text-20 text-sufra-text">
                                        مضغوط حاشي طازج
                                    </h3>
                                    <span class="font-heading font-semibold text-16 text-sufra-primary">
                                        48.00 ر.س
                                    </span>
                                </div>
                                <p class="text-14 text-sufra-muted">
                                    مطعم حاشي باشا المذاق &bull; حي الملز
                                </p>
                                <div class="flex items-center justify-between pt-2 border-t border-sufra-border/70 text-14">
                                    <span class="inline-flex items-center gap-1 text-sufra-primary font-medium">
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        <span>5.0 (تقييم ممتاز)</span>
                                    </span>
                                    <a
                                        href="{{ route('restaurants.show', 'hashi-basha') }}"
                                        class="inline-flex items-center gap-1 text-14 font-medium text-sufra-primary hover:underline"
                                    >
                                        <span>تصفح المنيو</span>
                                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ٢. شريط ركائز وضمانات سُفرة --}}
            <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded border border-sufra-border bg-sufra-bg flex items-center justify-center text-sufra-primary flex-shrink-0 mt-0.5">
                        <i data-lucide="chef-hat" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-heading font-semibold text-16 text-sufra-text">مطابخ محلية أصيلة</h4>
                        <p class="text-14 text-sufra-muted mt-1 leading-relaxed">
                            أطباق شعبية تطبخ طازجة عند طلبك بلحوم ودواجن يومية مضمونة.
                        </p>
                    </div>
                </div>

                <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded border border-sufra-border bg-sufra-bg flex items-center justify-center text-sufra-primary flex-shrink-0 mt-0.5">
                        <i data-lucide="activity" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-heading font-semibold text-16 text-sufra-text">تتبع مباشر لحظة بلحظة</h4>
                        <p class="text-14 text-sufra-muted mt-1 leading-relaxed">
                            شريط مراحل حي يرافقك من قبول الطلب وحتى وصول السائق لبابك.
                        </p>
                    </div>
                </div>

                <div class="bg-sufra-surface border border-sufra-border rounded p-4 flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded border border-sufra-border bg-sufra-bg flex items-center justify-center text-sufra-primary flex-shrink-0 mt-0.5">
                        <i data-lucide="receipt" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-heading font-semibold text-16 text-sufra-text">أسعار المنيو الحقيقية</h4>
                        <p class="text-14 text-sufra-muted mt-1 leading-relaxed">
                            أسعار مطابقة تماماً للمطعم، دون أي زيادات خفية أو مبالغات.
                        </p>
                    </div>
                </div>
            </section>

            {{-- ٣. قسم تصفح تصنيفات المطابخ (Cuisine Categories) --}}
            <section id="cuisines" class="space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-sufra-border">
                    <div>
                        <h2 class="font-heading font-semibold text-20 text-sufra-text">
                            استكشف حسب نوع المطبخ
                        </h2>
                        <p class="text-14 text-sufra-muted mt-0.5">اختر ما تشتهيه نفسك وتصفح المطابخ المتخصصة فيه</p>
                    </div>
                    <a href="{{ route('restaurants.index') }}" class="text-14 font-medium text-sufra-primary hover:underline flex items-center gap-1">
                        <span>كافة المطاعم</span>
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    {{-- 1. لحوم حاشي --}}
                    <a
                        href="{{ route('restaurants.index', ['cuisine' => 'لحوم حاشي']) }}"
                        class="bg-sufra-surface border border-sufra-border rounded p-3 text-center hover:border-sufra-primary transition-all group flex flex-col items-center"
                    >
                        <div class="w-14 h-14 rounded-full border border-sufra-border overflow-hidden bg-sufra-bg mb-2 flex items-center justify-center">
                            <img src="/storage/demo/dishes/madghoot-hashi.jpg" alt="لحوم حاشي" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        </div>
                        <span class="font-heading font-semibold text-14 text-sufra-text group-hover:text-sufra-primary">لحوم حاشي</span>
                        <span class="text-14 text-sufra-muted text-[12px] mt-0.5">مضغوط وكبسة</span>
                    </a>

                    {{-- 2. شعبي ونجدي --}}
                    <a
                        href="{{ route('restaurants.index', ['cuisine' => 'شعبي']) }}"
                        class="bg-sufra-surface border border-sufra-border rounded p-3 text-center hover:border-sufra-primary transition-all group flex flex-col items-center"
                    >
                        <div class="w-14 h-14 rounded-full border border-sufra-border overflow-hidden bg-sufra-bg mb-2 flex items-center justify-center">
                            <img src="/storage/demo/dishes/shawaya-half-peshawar.jpg" alt="شعبي ونجدي" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        </div>
                        <span class="font-heading font-semibold text-14 text-sufra-text group-hover:text-sufra-primary">شعبي ونجدي</span>
                        <span class="text-14 text-sufra-muted text-[12px] mt-0.5">شواية وجريش</span>
                    </a>

                    {{-- 3. بخاري وشواية --}}
                    <a
                        href="{{ route('restaurants.index', ['cuisine' => 'بخاري']) }}"
                        class="bg-sufra-surface border border-sufra-border rounded p-3 text-center hover:border-sufra-primary transition-all group flex flex-col items-center"
                    >
                        <div class="w-14 h-14 rounded-full border border-sufra-border overflow-hidden bg-sufra-bg mb-2 flex items-center justify-center">
                            <img src="/storage/demo/dishes/bukhari-coal-chicken.jpg" alt="بخاري وشواية" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        </div>
                        <span class="font-heading font-semibold text-14 text-sufra-text group-hover:text-sufra-primary">بخاري وشواية</span>
                        <span class="text-14 text-sufra-muted text-[12px] mt-0.5">دجاج ع الفحم</span>
                    </a>

                    {{-- 4. مشاوي ومندي --}}
                    <a
                        href="{{ route('restaurants.index', ['cuisine' => 'مشاوي']) }}"
                        class="bg-sufra-surface border border-sufra-border rounded p-3 text-center hover:border-sufra-primary transition-all group flex flex-col items-center"
                    >
                        <div class="w-14 h-14 rounded-full border border-sufra-border overflow-hidden bg-sufra-bg mb-2 flex items-center justify-center">
                            <img src="/storage/demo/dishes/mandi-meat-single.jpg" alt="مشاوي ومندي" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        </div>
                        <span class="font-heading font-semibold text-14 text-sufra-text group-hover:text-sufra-primary">مشاوي ومندي</span>
                        <span class="text-14 text-sufra-muted text-[12px] mt-0.5">كباب ومندي</span>
                    </a>

                    {{-- 5. شاورما وفطائر --}}
                    <a
                        href="{{ route('restaurants.index', ['cuisine' => 'شاورما']) }}"
                        class="bg-sufra-surface border border-sufra-border rounded p-3 text-center hover:border-sufra-primary transition-all group flex flex-col items-center"
                    >
                        <div class="w-14 h-14 rounded-full border border-sufra-border overflow-hidden bg-sufra-bg mb-2 flex items-center justify-center">
                            <img src="/storage/demo/dishes/shawarma-sarookh.jpg" alt="شاورما وفطائر" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        </div>
                        <span class="font-heading font-semibold text-14 text-sufra-text group-hover:text-sufra-primary">شاورما وفطائر</span>
                        <span class="text-14 text-sufra-muted text-[12px] mt-0.5">صاروخ ومطبق</span>
                    </a>

                    {{-- 6. حلويات وعصائر --}}
                    <a
                        href="{{ route('restaurants.index', ['cuisine' => 'حلويات وعصائر']) }}"
                        class="bg-sufra-surface border border-sufra-border rounded p-3 text-center hover:border-sufra-primary transition-all group flex flex-col items-center"
                    >
                        <div class="w-14 h-14 rounded-full border border-sufra-border overflow-hidden bg-sufra-bg mb-2 flex items-center justify-center">
                            <img src="/storage/demo/dishes/kunafa-cream.jpg" alt="حلويات وعصائر" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        </div>
                        <span class="font-heading font-semibold text-14 text-sufra-text group-hover:text-sufra-primary">حلويات وعصائر</span>
                        <span class="text-14 text-sufra-muted text-[12px] mt-0.5">كنافة وعصائر</span>
                    </a>
                </div>
            </section>

            {{-- ٤. قائمة المطاعم المحلية المتاحة (صفوف أفقية أنيقة بحسب DESIGN.md) --}}
            <section class="space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-sufra-border">
                    <div>
                        <h2 class="font-heading font-semibold text-20 text-sufra-text">
                            المطاعم المتاحة للطلب الآن
                        </h2>
                        <p class="text-14 text-sufra-muted mt-0.5">مطابخ نشطة وجاهزة لاستقبال طلبك وإعداده فورياً</p>
                    </div>
                    <a href="{{ route('restaurants.index') }}" class="text-14 font-medium text-sufra-primary hover:underline flex items-center gap-1">
                        <span>عرض كافة المطاعم ({{ \App\Models\Restaurant::active()->count() }})</span>
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach ($restaurants as $restaurant)
                        <a
                            href="{{ route('restaurants.show', $restaurant->slug) }}"
                            class="block bg-sufra-surface border border-sufra-border rounded p-4 hover:border-sufra-primary transition-all duration-150 {{ ! $restaurant->is_open ? 'opacity-75' : '' }}"
                        >
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                {{-- الجزء الأيمن: الشعار والمعلومات --}}
                                <div class="flex items-start sm:items-center gap-4 min-w-0">
                                    {{-- صورة الشعار --}}
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded border border-sufra-border overflow-hidden bg-sufra-bg flex-shrink-0">
                                        <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                                    </div>

                                    {{-- الاسم ونوع المطبخ والحي --}}
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap mb-1">
                                            <span class="font-heading font-semibold text-16 sm:text-20 text-sufra-text">
                                                {{ $restaurant->name }}
                                            </span>
                                            <x-badge variant="neutral">{{ $restaurant->cuisine }}</x-badge>
                                            
                                            {{-- تقييم النجوم --}}
                                            <span class="inline-flex items-center gap-1 text-14 text-sufra-primary font-medium bg-sufra-bg px-2 py-0.5 rounded border border-sufra-border">
                                                <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                                <span>{{ $restaurant->average_rating }}</span>
                                                <span class="text-sufra-muted text-[12px]">({{ $restaurant->reviews_count }})</span>
                                            </span>

                                            @if ($restaurant->is_open)
                                                <x-badge variant="primary" icon="check-circle">مفتوح للطلب</x-badge>
                                            @else
                                                <x-badge variant="neutral" icon="clock">مغلق حالياً</x-badge>
                                            @endif
                                        </div>

                                        @if ($restaurant->description)
                                            <p class="text-14 text-sufra-muted line-clamp-1 mb-2">
                                                {{ $restaurant->description }}
                                            </p>
                                        @endif

                                        {{-- معلومات الحي والتوصيل والحد الأدنى --}}
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
                                                <span>{{ $restaurant->formatted_delivery_fee }}</span>
                                            </span>
                                            <span>&bull;</span>
                                            <span>الحد الأدنى: {{ $restaurant->formatted_min_order }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- الجزء الأيسر: رابط المنيو --}}
                                <div class="flex items-center justify-end pt-2 sm:pt-0 border-t sm:border-t-0 border-sufra-border flex-shrink-0">
                                    <span class="inline-flex items-center gap-1 text-14 font-medium text-sufra-primary hover:underline px-3 py-1.5 rounded border border-sufra-border bg-sufra-bg">
                                        <span>تصفح المنيو</span>
                                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- ٥. قسم طريقة الطلب (How it works) --}}
            <section id="how-it-works" class="bg-sufra-surface border border-sufra-border rounded p-6 sm:p-8 space-y-6">
                <div class="text-center max-w-xl mx-auto space-y-2">
                    <h3 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">
                        كيف تطلب وجبتك من سُفرة؟
                    </h3>
                    <p class="text-14 text-sufra-muted">
                        ثلاث خطوات بسيطة ومباشرة تضمن لك وصول أشهى الأطباق طازجة وساخنة
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                    <div class="text-center p-4 space-y-2.5">
                        <div class="w-12 h-12 rounded-full border border-sufra-primary bg-sufra-bg text-sufra-primary flex items-center justify-center font-heading font-semibold text-20 mx-auto">
                            ١
                        </div>
                        <h4 class="font-heading font-semibold text-16 text-sufra-text">تصفح المنيو الورقي</h4>
                        <p class="text-14 text-sufra-muted leading-relaxed">
                            اختر مطبخك المفضل واستعرض أطباقه المقسمة بتصنيفات واضحة مع كامل التفاصيل والأسعار.
                        </p>
                    </div>

                    <div class="text-center p-4 space-y-2.5">
                        <div class="w-12 h-12 rounded-full border border-sufra-primary bg-sufra-bg text-sufra-primary flex items-center justify-center font-heading font-semibold text-20 mx-auto">
                            ٢
                        </div>
                        <h4 class="font-heading font-semibold text-16 text-sufra-text">خصص طلبك واعتمد السلة</h4>
                        <p class="text-14 text-sufra-muted leading-relaxed">
                            أضف إضافاتك المفضلة وملاحظاتك الخاصة للشيف، وطبق كود الكوبون لتوفير مباشر في الإجمالي.
                        </p>
                    </div>

                    <div class="text-center p-4 space-y-2.5">
                        <div class="w-12 h-12 rounded-full border border-sufra-primary bg-sufra-bg text-sufra-primary flex items-center justify-center font-heading font-semibold text-20 mx-auto">
                            ٣
                        </div>
                        <h4 class="font-heading font-semibold text-16 text-sufra-text">تتبع مباشر واستلام حار</h4>
                        <p class="text-14 text-sufra-muted leading-relaxed">
                            تابع مراحل تجهيز وخروج طلبك عبر شريط التتبع الحي حتى يطرق السائق بابك ويسلمك وجبتك.
                        </p>
                    </div>
                </div>
            </section>

            {{-- ٦. قسم آراء العملاء وتجاربهم --}}
            <section class="space-y-4">
                <div class="text-center max-w-xl mx-auto space-y-1">
                    <h3 class="font-heading font-semibold text-20 text-sufra-text">
                        آراء وتجارب عملاء سُفرة
                    </h3>
                    <p class="text-14 text-sufra-muted">
                        ثقة عملائنا هي سر نجاحنا وتميز المطابخ الشريكة معنا
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div class="bg-sufra-surface border border-sufra-border rounded p-4 space-y-3">
                        <div class="flex items-center gap-1 text-sufra-primary">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-14 text-sufra-text leading-relaxed">
                            "مضغوط الحاشي وصل حاراً جداً واللحم ذائب والأرز نثري ومضبوط بالمللي. تجربة ممتازة وتستحق التكرار!"
                        </p>
                        <div class="pt-2 border-t border-sufra-border text-14 flex items-center justify-between text-sufra-muted">
                            <span class="font-medium text-sufra-text">سلطان الشمري</span>
                            <span>طلب من: حاشي باشا</span>
                        </div>
                    </div>

                    <div class="bg-sufra-surface border border-sufra-border rounded p-4 space-y-3">
                        <div class="flex items-center gap-1 text-sufra-primary">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-14 text-sufra-text leading-relaxed">
                            "أفضل دجاج شواية في شمال الرياض، التغليف نظيف والتوصيل وصل قبل الوقت المتوقع بنحو عشر دقائق."
                        </p>
                        <div class="pt-2 border-t border-sufra-border text-14 flex items-center justify-between text-sufra-muted">
                            <span class="font-medium text-sufra-text">فيصل القحطاني</span>
                            <span>طلب من: شواية نجد</span>
                        </div>
                    </div>

                    <div class="bg-sufra-surface border border-sufra-border rounded p-4 space-y-3">
                        <div class="flex items-center gap-1 text-sufra-primary">
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        </div>
                        <p class="text-14 text-sufra-text leading-relaxed">
                            "واجهة الموقع هادئة جداً ومريحة للعين، وتجربة السلة بدون إعادة تحميل سهلت علي الطلب تماماً."
                        </p>
                        <div class="pt-2 border-t border-sufra-border text-14 flex items-center justify-between text-sufra-muted">
                            <span class="font-medium text-sufra-text">نورة الدوسري</span>
                            <span>طلب من: أم نورة</span>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        {{-- تذييل الصفحة الرسمي --}}
        <footer class="bg-sufra-surface border-t border-sufra-border py-8 mt-12">
            <div class="max-w-sufra mx-auto px-4 sm:px-6 space-y-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 border-b border-sufra-border">
                    <div class="flex items-center gap-3">
                        <x-application-logo />
                        <span class="text-14 text-sufra-muted">منصة طلبات المطاعم والمطابخ السعودية</span>
                    </div>

                    <div class="flex items-center gap-6 text-14 text-sufra-muted">
                        <a href="{{ route('restaurants.index') }}" class="hover:text-sufra-text transition-colors">استكشف المطاعم</a>
                        <a href="#how-it-works" class="hover:text-sufra-text transition-colors">كيف تطلب؟</a>
                        <a href="{{ route('login') }}" class="hover:text-sufra-text transition-colors">دخول أصحاب المطاعم</a>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-14 text-sufra-muted">
                    <span>جميع الحقوق محفوظة &bull; سُفرة {{ date('Y') }}</span>
                    <span class="font-medium text-sufra-muted">نموذج عرض تجريبي - جميع البيانات وهمية</span>
                </div>
            </div>
        </footer>

        {{-- درج السلة الجانبي المنزلق --}}
        <livewire:customer.cart-drawer />

        @livewireScripts
    </body>
</html>
