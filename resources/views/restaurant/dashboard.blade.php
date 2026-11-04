<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-heading font-semibold text-28 text-sufra-text">لوحة تحكم المطعم</h1>
                <p class="text-14 text-sufra-muted mt-1">مرحباً {{ Auth::user()->name }}، يمكنك متابعة طلباتك وقائمة طعامك هنا.</p>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sufra-surface border border-sufra-border rounded text-14 text-sufra-primary font-medium">
                <i data-lucide="store" class="w-4 h-4"></i>
                <span>حساب مطعم</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- بطاقة ترحيبية مؤقتة للمرحلة 1 --}}
        <div class="bg-sufra-surface border border-sufra-border rounded p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded border border-sufra-border bg-sufra-bg flex items-center justify-center text-sufra-primary flex-shrink-0">
                    <i data-lucide="utensils" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="font-heading font-semibold text-20 text-sufra-text">لوحة إدارة المطعم (/restaurant)</h2>
                    <p class="text-16 text-sufra-muted mt-2 leading-relaxed">
                        هذه الصفحة مؤقتة ومحمية بـ Middleware الأدوار (<code class="text-14 px-1.5 py-0.5 bg-sufra-bg rounded border border-sufra-border">role:restaurant</code>).
                        سيتم بناء إدارة المنيو والطلبات اللحظية وحالة استقبال الطلبات في المراحل التالية.
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-sufra-border flex items-center justify-between text-14 text-sufra-muted">
                <span>المسار الحالي: <strong class="text-sufra-text">/restaurant</strong></span>
                <span>الحالة: <span class="text-sufra-primary font-medium">نشط ومحمي</span></span>
            </div>
        </div>
    </div>
</x-app-layout>

