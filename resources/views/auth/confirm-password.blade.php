<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-heading font-semibold text-20 text-sufra-text">تأكيد كلمة المرور</h1>
        <p class="mt-1 text-14 text-sufra-muted">
            هذه المنطقة محمية. يُرجى تأكيد كلمة المرور الخاصة بك للمتابعة.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <!-- كلمة المرور -->
        <div>
            <x-input-label for="password" value="كلمة المرور" />
            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full py-2.5 text-16">
                تأكيد المتابعة
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
