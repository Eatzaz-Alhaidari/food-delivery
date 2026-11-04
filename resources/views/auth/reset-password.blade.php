<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-heading font-semibold text-20 text-sufra-text">تعيين كلمة المرور الجديدة</h1>
        <p class="mt-1 text-14 text-sufra-muted">أدخل كلمة المرور الجديدة لحسابك لتتمكن من المتابعة.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- رمز الاستعادة -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- البريد الإلكتروني -->
        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- كلمة المرور -->
        <div>
            <x-input-label for="password" value="كلمة المرور الجديدة" />
            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- تأكيد كلمة المرور -->
        <div>
            <x-input-label for="password_confirmation" value="تأكيد كلمة المرور الجديدة" />
            <x-text-input id="password_confirmation" class="block w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full py-2.5 text-16">
                حفظ كلمة المرور الجديدة
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
