<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-heading font-semibold text-20 text-sufra-text">تسجيل الدخول</h1>
        <p class="mt-1 text-14 text-sufra-muted">أهلاً بك مجدداً في سُفرة. أدخل بياناتك للمتابعة.</p>
    </div>

    <!-- حالة الجلسة (رسائل النجاح) -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- البريد الإلكتروني -->
        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- كلمة المرور -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <x-input-label for="password" value="كلمة المرور" class="!mb-0" />
                @if (Route::has('password.request'))
                    <a class="text-14 text-sufra-muted hover:text-sufra-primary transition-colors duration-150" href="{{ route('password.request') }}">
                        نسيت كلمة المرور؟
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- تذكّرني -->
        <div class="pt-1">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-sufra-border text-sufra-primary focus:ring-sufra-primary bg-sufra-surface focus:ring-offset-0" name="remember">
                <span class="text-14 text-sufra-text">تذكّرني على هذا الجهاز</span>
            </label>
        </div>

        <!-- زر الدخول -->
        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full py-2.5 text-16">
                تسجيل الدخول
            </x-button>
        </div>

        <!-- رابط التسجيل -->
        <div class="pt-4 mt-4 border-t border-sufra-border text-center text-14 text-sufra-muted">
            ليس لديك حساب بعد؟
            <a href="{{ route('register') }}" class="text-sufra-primary font-medium hover:underline ms-1">
                أنشئ حساباً جديداً
            </a>
        </div>
    </form>
</x-guest-layout>
