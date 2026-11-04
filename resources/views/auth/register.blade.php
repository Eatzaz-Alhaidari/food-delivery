<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-heading font-semibold text-20 text-sufra-text">إنشاء حساب جديد</h1>
        <p class="mt-1 text-14 text-sufra-muted">انضم إلى سُفرة واطلب وجباتك المفضلة بكل سهولة.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- الاسم -->
        <div>
            <x-input-label for="name" value="الاسم الكامل" />
            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="مثال: تركي السعد" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- البريد الإلكتروني -->
        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- كلمة المرور -->
        <div>
            <x-input-label for="password" value="كلمة المرور" />
            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- تأكيد كلمة المرور -->
        <div>
            <x-input-label for="password_confirmation" value="تأكيد كلمة المرور" />
            <x-text-input id="password_confirmation" class="block w-full"
                            type="password"
                            name="password_confirmation"
                            required autocomplete="new-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- زر التسجيل -->
        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full py-2.5 text-16">
                إنشاء الحساب
            </x-button>
        </div>

        <!-- رابط تسجيل الدخول -->
        <div class="pt-4 mt-4 border-t border-sufra-border text-center text-14 text-sufra-muted">
            لديك حساب بالفعل؟
            <a href="{{ route('login') }}" class="text-sufra-primary font-medium hover:underline ms-1">
                تسجيل الدخول
            </a>
        </div>
    </form>
</x-guest-layout>
