<section>
    <header class="pb-3 border-b border-sufra-border">
        <h2 class="font-heading font-semibold text-16 text-sufra-text">
            معلومات الملف الشخصي
        </h2>

        <p class="mt-1 text-14 text-sufra-muted">
            قم بتحديث اسم الحساب وعنوان البريد الإلكتروني الخاص بك.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="الاسم الكامل" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 text-14 text-sufra-muted">
                    <p>
                        بريدك الإلكتروني غير مفعّل.
                        <button form="send-verification" class="text-sufra-primary underline hover:text-sufra-primary-hover font-medium">
                            اضغط هنا لإعادة إرسال رابط التفعيل.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-14 text-sufra-primary">
                            تم إرسال رابط تفعيل جديد إلى بريدك الإلكتروني.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-button type="submit" variant="primary" icon="check">حفظ التغييرات</x-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-14 text-sufra-primary font-medium flex items-center gap-1"
                >
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>تم الحفظ بنجاح.</span>
                </p>
            @endif
        </div>
    </form>
</section>
