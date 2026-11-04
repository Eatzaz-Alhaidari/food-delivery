<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'لوحة المطعم' }} | {{ config('app.name', 'سُفرة') }}</title>

        <!-- Google Fonts: IBM Plex Sans Arabic & Noto Kufi Arabic -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500&family=Noto+Kufi+Arabic:wght@600&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-sufra-bg text-sufra-text font-sans antialiased min-h-screen flex flex-col">
        {{-- شريط الرأس العلوي للوحة المطعم --}}
        <header class="bg-sufra-surface border-b border-sufra-border sticky top-0 z-30">
            <div class="max-w-sufra mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="/" class="flex items-center gap-2">
                        <x-application-logo />
                    </a>
                    <span class="text-sufra-border">|</span>
                    <span class="font-heading font-semibold text-16 text-sufra-text">لوحة المطعم</span>
                </div>

                <div class="flex items-center gap-3">
                    @php
                        $userRestaurant = Auth::user()->restaurant;
                    @endphp

                    @if ($userRestaurant)
                        <div class="hidden sm:flex items-center gap-2 text-14">
                            <span class="font-medium text-sufra-text">{{ $userRestaurant->name }}</span>
                            <span class="text-sufra-muted">({{ $userRestaurant->district }})</span>
                            @if ($userRestaurant->is_open)
                                <x-badge variant="primary" icon="check-circle">مفتوح</x-badge>
                            @else
                                <x-badge variant="neutral" icon="clock">مغلق</x-badge>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <x-button type="submit" variant="secondary" size="sm" icon="log-out">
                            خروج
                        </x-button>
                    </form>
                </div>
            </div>
        </header>

        {{-- تخطيط اللوحة: شريط جانبي ومحتوى --}}
        <div class="max-w-sufra w-full mx-auto px-4 sm:px-6 py-6 flex-1 flex flex-col md:flex-row gap-6">
            {{-- الشريط الجانبي (Sidebar) بحسب DESIGN.md --}}
            <aside class="w-full md:w-60 flex-shrink-0">
                <nav class="bg-sufra-surface border border-sufra-border rounded p-2 space-y-1">
                    {{-- تبويب المنيو --}}
                    <a href="{{ route('restaurant.menu') }}"
                       class="flex items-center gap-2.5 px-3.5 py-2.5 rounded text-14 font-medium transition-colors duration-150 {{ request()->routeIs('restaurant.menu') ? 'bg-sufra-bg text-sufra-primary border border-sufra-border font-semibold' : 'text-sufra-text hover:bg-sufra-bg' }}">
                        <i data-lucide="utensils" class="w-4 h-4 text-sufra-primary"></i>
                        <span>قائمة الطعام (المنيو)</span>
                    </a>

                    {{-- تبويب بيانات المطعم --}}
                    <a href="{{ route('restaurant.profile') }}"
                       class="flex items-center gap-2.5 px-3.5 py-2.5 rounded text-14 font-medium transition-colors duration-150 {{ request()->routeIs('restaurant.profile') ? 'bg-sufra-bg text-sufra-primary border border-sufra-border font-semibold' : 'text-sufra-text hover:bg-sufra-bg' }}">
                        <i data-lucide="store" class="w-4 h-4 text-sufra-primary"></i>
                        <span>بيانات المطعم</span>
                    </a>

                    {{-- تبويب الطلبات الواردة --}}
                    @php
                        $sidebarPending = $userRestaurant ? $userRestaurant->orders()->where('status', \App\Enums\OrderStatus::Pending->value)->count() : 0;
                    @endphp
                    <a href="{{ route('restaurant.orders') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded text-14 font-medium transition-colors duration-150 {{ request()->routeIs('restaurant.orders') ? 'bg-sufra-bg text-sufra-primary border border-sufra-border font-semibold' : 'text-sufra-text hover:bg-sufra-bg' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="shopping-bag" class="w-4 h-4 text-sufra-primary"></i>
                            <span>الطلبات الواردة</span>
                        </div>
                        @if ($sidebarPending > 0)
                            <span class="text-14 px-2 py-0.5 rounded bg-sufra-accent text-sufra-surface font-semibold">{{ $sidebarPending }}</span>
                        @endif
                    </a>
                </nav>

                {{-- بطاقة إرشادية خفيفة بنمط ورق المنيو --}}
                <div class="mt-4 p-4 rounded border border-sufra-border bg-sufra-surface text-14 text-sufra-muted leading-relaxed hidden md:block">
                    <div class="font-medium text-sufra-text mb-1 flex items-center gap-1.5">
                        <i data-lucide="info" class="w-4 h-4 text-sufra-primary"></i>
                        <span>ملاحظة</span>
                    </div>
                    التعديلات على المنيو وبيانات المطعم تنعكس لحظياً على واجهة العملاء.
                </div>
            </aside>

            {{-- المحتوى الرئيسي للوحة --}}
            <main class="flex-1 min-w-0">
                {{ $slot }}
            </main>
        </div>

        {{-- تذييل --}}
        <footer class="bg-sufra-surface border-t border-sufra-border py-4 mt-auto">
            <div class="max-w-sufra mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-14 text-sufra-muted">
                <span>سُفرة &bull; لوحة إدارة المطعم</span>
                <span>نموذج عرض تجريبي - جميع البيانات وهمية</span>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>

