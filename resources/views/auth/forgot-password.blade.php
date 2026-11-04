<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-heading font-semibold text-20 text-sufra-text">استعادة كلمة المرور</h1>
        <p class="mt-1 text-14 text-sufra-muted">
            أدخل بريدك الإلكتروني المسجل، وسنرسل لك رابطاً لإعادة تعيين كلمة المرور بكل يسر.
        </p>
    </div>

    <!-- حالة الجلسة (رسائل النجاح) -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- البريد الإلكتروني -->
        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full py-2.5 text-16">
                إرسال رابط الاستعادة
            </x-primary-button>
        </div>

        <div class="pt-4 mt-4 border-t border-sufra-border text-center text-14 text-sufra-muted">
            تذكرت كلمة المرور؟
            <a href="{{ route('login') }}" class="text-sufra-primary font-medium hover:underline ms-1">
                العودة لتسجيل الدخول
            </a>
        </div>
    </form>
</x-guest-layout>
