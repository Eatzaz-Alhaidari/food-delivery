<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'سُفرة') }}</title>

        <!-- Google Fonts: IBM Plex Sans Arabic & Noto Kufi Arabic -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500&family=Noto+Kufi+Arabic:wght@600&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-sufra-bg text-sufra-text font-sans antialiased min-h-screen">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- رأس الصفحة إن وُجد -->
            @isset($header)
                <header class="bg-sufra-surface border-b border-sufra-border">
                    <div class="max-w-sufra mx-auto py-5 px-4 sm:px-6">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- محتوى الصفحة بعمود قراءة أقصاه 1100px -->
            <main class="flex-1 max-w-sufra w-full mx-auto p-4 sm:p-6">
                {{ $slot }}
            </main>

            {{-- تذييل الصفحة --}}
            <footer class="bg-sufra-surface border-t border-sufra-border py-5 mt-auto">
                <div class="max-w-sufra mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-14 text-sufra-muted">
                    <span>سُفرة &bull; منصة طلبات المطاعم والمطابخ</span>
                    <span class="text-sufra-muted font-medium">نموذج عرض تجريبي - جميع البيانات وهمية</span>
                </div>
            </footer>
        </div>

        {{-- درج السلة الجانبي المنزلق لجميع صفحات التطبيق --}}
        <livewire:customer.cart-drawer />

        @livewireScripts
    </body>
</html>
