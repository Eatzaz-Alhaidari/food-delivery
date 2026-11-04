<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-heading font-semibold text-20 text-sufra-text">تأكيد البريد الإلكتروني</h1>
        <p class="mt-1 text-14 text-sufra-muted">
            شكراً لانضمامك إلى سُفرة. قبل البدء، يُرجى تأكيد بريدك الإلكتروني عبر النقر على الرابط الذي أرسلناه لك.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 bg-sufra-surface border border-sufra-primary text-sufra-primary px-3.5 py-2.5 rounded text-14 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
            <span>تم إرسال رابط تأكيد جديد إلى عنوان البريد الإلكتروني الذي سجلت به.</span>
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full py-2.5 text-16">
                إعادة إرسال رابط التأكيد
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center pt-2 border-t border-sufra-border">
            @csrf
            <button type="submit" class="text-14 text-sufra-muted hover:text-sufra-primary transition-colors duration-150">
                تسجيل الخروج
            </button>
        </form>
    </div>
</x-guest-layout>
