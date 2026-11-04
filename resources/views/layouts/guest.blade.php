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
    <body class="bg-sufra-bg text-sufra-text font-sans min-h-screen antialiased flex flex-col items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-[440px] flex flex-col items-center">
            {{-- الشعار ورابط العودة للرئيسية --}}
            <div class="mb-6">
                <a href="/" class="inline-block transition-opacity hover:opacity-80">
                    <x-application-logo />
                </a>
            </div>

            {{-- بطاقة المحتوى بنمط الورق المطبوع والحدود الرفيعة --}}
            <div class="w-full bg-sufra-surface border border-sufra-border rounded p-6 sm:p-8">
                {{ $slot }}
            </div>

            {{-- تذييل هادئ وبسيط --}}
            <div class="mt-6 text-center text-14 text-sufra-muted">
                سُفرة &bull; منصة طلبات المطاعم
            </div>
        </div>

        @livewireScripts
    </body>
</html>
