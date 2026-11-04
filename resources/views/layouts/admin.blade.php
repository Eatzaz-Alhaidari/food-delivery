<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'لوحة الإدارة' }} — {{ config('app.name', 'سُفرة') }}</title>

        <!-- Google Fonts: IBM Plex Sans Arabic & Noto Kufi Arabic -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500&family=Noto+Kufi+Arabic:wght@600&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-sufra-bg text-sufra-text font-sans antialiased min-h-screen flex flex-col">
        {{-- شريط التنقل العلوي للوحة الإدارة --}}
        <header class="bg-sufra-surface border-b border-sufra-border sticky top-0 z-30">
            <div class="max-w-sufra mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo />
                    </a>
                    <span class="text-14 text-sufra-muted border-s border-sufra-border ps-3 hidden sm:inline">
                        لوحة إدارة المنصة
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <x-badge variant="solid-olive" icon="shield-check">
                        مدير المنصة
                    </x-badge>

                    <span class="text-14 text-sufra-muted hidden md:inline">
                        {{ Auth::user()->name }}
                    </span>

                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-sufra-border text-14 font-medium text-sufra-text hover:bg-sufra-bg transition-colors"
                        title="معاينة واجهة المتجر العامة"
                    >
                        <i data-lucide="external-link" class="w-3.5 h-3.5 text-sufra-muted"></i>
                        <span>واجهة المتجر</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <x-button type="submit" variant="secondary" size="sm" icon="log-out">
                            خروج
                        </x-button>
                    </form>
                </div>
            </div>
        </header>

        {{-- تخطيط لوحة الإدارة: شريط جانبي ومحتوى رئيسي --}}
        <div class="max-w-sufra w-full mx-auto px-4 sm:px-6 py-6 flex-1 flex flex-col md:flex-row gap-6">
            {{-- الشريط الجانبي (Sidebar) بحسب DESIGN.md --}}
            <aside class="w-full md:w-60 flex-shrink-0">
                <nav class="bg-sufra-surface border border-sufra-border rounded p-2 space-y-1">
                    {{-- لوحة المؤشرات الرئيسية --}}
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-2.5 px-3.5 py-2.5 rounded text-14 font-medium transition-colors duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-sufra-bg text-sufra-primary border border-sufra-border font-semibold' : 'text-sufra-text hover:bg-sufra-bg' }}"
                    >
                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-sufra-primary"></i>
                        <span>لوحة المؤشرات</span>
                    </a>

                    {{-- إدارة المطاعم --}}
                    <a
                        href="{{ route('admin.restaurants') }}"
                        class="flex items-center gap-2.5 px-3.5 py-2.5 rounded text-14 font-medium transition-colors duration-150 {{ request()->routeIs('admin.restaurants*') ? 'bg-sufra-bg text-sufra-primary border border-sufra-border font-semibold' : 'text-sufra-text hover:bg-sufra-bg' }}"
                    >
                        <i data-lucide="store" class="w-4 h-4 text-sufra-primary"></i>
                        <span>إدارة المطاعم</span>
                    </a>

                    {{-- سجل كافة الطلبات --}}
                    <a
                        href="{{ route('admin.orders') }}"
                        class="flex items-center gap-2.5 px-3.5 py-2.5 rounded text-14 font-medium transition-colors duration-150 {{ request()->routeIs('admin.orders*') ? 'bg-sufra-bg text-sufra-primary border border-sufra-border font-semibold' : 'text-sufra-text hover:bg-sufra-bg' }}"
                    >
                        <i data-lucide="shopping-bag" class="w-4 h-4 text-sufra-primary"></i>
                        <span>كافة الطلبات</span>
                    </a>

                    {{-- إدارة الكوبونات (المرحلة 8) --}}
                    <a
                        href="{{ route('admin.coupons') }}"
                        class="flex items-center gap-2.5 px-3.5 py-2.5 rounded text-14 font-medium transition-colors duration-150 {{ request()->routeIs('admin.coupons*') ? 'bg-sufra-bg text-sufra-primary border border-sufra-border font-semibold' : 'text-sufra-text hover:bg-sufra-bg' }}"
                    >
                        <i data-lucide="tag" class="w-4 h-4 text-sufra-primary"></i>
                        <span>إدارة الكوبونات</span>
                    </a>

                    {{-- المبيعات والعمولة --}}
                    <a
                        href="{{ route('admin.sales') }}"
                        class="flex items-center gap-2.5 px-3.5 py-2.5 rounded text-14 font-medium transition-colors duration-150 {{ request()->routeIs('admin.sales*') ? 'bg-sufra-bg text-sufra-primary border border-sufra-border font-semibold' : 'text-sufra-text hover:bg-sufra-bg' }}"
                    >
                        <i data-lucide="bar-chart-3" class="w-4 h-4 text-sufra-primary"></i>
                        <span>المبيعات والعمولة</span>
                    </a>

                    {{-- إعدادات المنصة --}}
                    <a
                        href="{{ route('admin.settings') }}"
                        class="flex items-center gap-2.5 px-3.5 py-2.5 rounded text-14 font-medium transition-colors duration-150 {{ request()->routeIs('admin.settings*') ? 'bg-sufra-bg text-sufra-primary border border-sufra-border font-semibold' : 'text-sufra-text hover:bg-sufra-bg' }}"
                    >
                        <i data-lucide="settings" class="w-4 h-4 text-sufra-primary"></i>
                        <span>إعدادات المنصة</span>
                    </a>
                </nav>

                {{-- بطاقة إحصائية موجزة --}}
                <div class="mt-4 p-4 rounded border border-sufra-border bg-sufra-surface text-14 text-sufra-muted leading-relaxed hidden md:block">
                    <div class="font-medium text-sufra-text mb-1 flex items-center gap-1.5">
                        <i data-lucide="percent" class="w-4 h-4 text-sufra-primary"></i>
                        <span>عمولة المنصة</span>
                    </div>
                    النسبة المعمول بها حالياً هي <strong>{{ \App\Models\PlatformSetting::getCommissionPercentage() }}%</strong> من صافي مبيعات كل طلب مكتمل.
                </div>
            </aside>

            {{-- المحتوى الرئيسي للوحة --}}
            <main class="flex-1 min-w-0">
                {{ $slot }}
            </main>
        </div>

        {{-- تذييل لوحة المدير --}}
        <footer class="bg-sufra-surface border-t border-sufra-border py-4 mt-auto">
            <div class="max-w-sufra mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-14 text-sufra-muted">
                <span>سُفرة &bull; لوحة إدارة المنصة</span>
                <span>نموذج عرض تجريبي - جميع البيانات وهمية</span>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>

