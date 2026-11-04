<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>٤٠٤ — الصفحة غير موجودة | {{ config('app.name', 'سُفرة') }}</title>

        <!-- Google Fonts: IBM Plex Sans Arabic & Noto Kufi Arabic -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500&family=Noto+Kufi+Arabic:wght@600&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-sufra-bg text-sufra-text font-sans min-h-screen antialiased flex flex-col items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-[480px] flex flex-col items-center">
            {{-- الشعار --}}
            <div class="mb-6">
                <a href="/" class="inline-block transition-opacity hover:opacity-80">
                    <x-application-logo />
                </a>
            </div>

            {{-- بطاقة الخطأ الهادئة بنمط ورق المنيو المطبوع --}}
            <div class="w-full bg-sufra-surface border border-sufra-border rounded p-8 text-center shadow-none">
                {{-- أيقونة البحث / الصفحة المفقودة باللون الزيتي الهادئ --}}
                <div class="w-14 h-14 rounded border border-sufra-border bg-sufra-bg flex items-center justify-center text-sufra-primary mx-auto mb-5">
                    <i data-lucide="compass" class="w-7 h-7"></i>
                </div>

                {{-- شارة رقم الخطأ بخط Noto Kufi --}}
                <div class="inline-block px-2.5 py-0.5 rounded border border-sufra-border text-14 font-medium text-sufra-muted mb-3">
                    خطأ ٤٠٤
                </div>

                <h1 class="font-heading font-semibold text-28 text-sufra-text">
                    الصفحة غير موجودة
                </h1>

                <p class="text-16 text-sufra-muted mt-3 leading-relaxed">
                    عذراً، الصفحة أو الرابط الذي تحاول الوصول إليه غير متوفر حالياً، أو ربما تم تغييره أو نقله.
                </p>

                {{-- إجراءات العودة --}}
                <div class="mt-8 pt-6 border-t border-sufra-border flex flex-col sm:flex-row items-center justify-center gap-3">
                    <x-button href="/" variant="primary" icon="home" class="w-full sm:w-auto">
                        الرئيسية
                    </x-button>

                    <x-button href="{{ route('restaurants.index') }}" variant="secondary" icon="store" class="w-full sm:w-auto">
                        استكشف المطاعم
                    </x-button>
                </div>
            </div>

            {{-- تذييل --}}
            <div class="mt-6 text-center text-14 text-sufra-muted">
                سُفرة &bull; نموذج عرض تجريبي - جميع البيانات وهمية
            </div>
        </div>
    </body>
</html>

