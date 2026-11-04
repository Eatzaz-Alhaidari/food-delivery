<section>
    <header class="pb-3 border-b border-sufra-border">
        <h2 class="font-heading font-semibold text-16 text-sufra-text">
            تحديث كلمة المرور
        </h2>

        <p class="mt-1 text-14 text-sufra-muted">
            احرص على استخدام كلمة مرور قوية وغير مكررة لحماية حسابك.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="كلمة المرور الحالية" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="كلمة المرور الجديدة" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="تأكيد كلمة المرور الجديدة" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-button type="submit" variant="primary" icon="lock">تحديث كلمة المرور</x-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-14 text-sufra-primary font-medium flex items-center gap-1"
                >
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>تم التحديث بنجاح.</span>
                </p>
            @endif
        </div>
    </form>
</section>
