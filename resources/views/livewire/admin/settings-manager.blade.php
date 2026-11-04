<div class="max-w-2xl space-y-6">
    {{-- رأس الصفحة --}}
    <div class="pb-4 border-b border-sufra-border">
        <h1 class="font-heading font-semibold text-20 sm:text-28 text-sufra-text">إعدادات المنصة</h1>
        <p class="text-14 text-sufra-muted mt-1">تحديد السياسات والنسب المالية التشغيلية لمنصة سُفرة.</p>
    </div>

    {{-- رسالة نجاح الحفظ --}}
    @if ($feedbackMessage)
        <div class="p-4 rounded border border-sufra-border bg-sufra-surface text-14 text-sufra-primary flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-sufra-primary"></i>
                <span>{{ $feedbackMessage }}</span>
            </div>
            <button type="button" wire:click="$set('feedbackMessage', null)" class="text-sufra-muted hover:text-sufra-text">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    {{-- بطاقة إعداد عمولة المنصة (المتطلب 6) --}}
    <div class="bg-sufra-surface border border-sufra-border rounded p-6 space-y-5">
        <div class="flex items-start gap-3 pb-4 border-b border-sufra-border">
            <div class="w-10 h-10 rounded border border-sufra-border bg-sufra-bg flex items-center justify-center text-sufra-primary flex-shrink-0">
                <i data-lucide="percent" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="font-heading font-semibold text-16 text-sufra-text">عمولة المنصة من مبيعات المطاعم</h2>
                <p class="text-14 text-sufra-muted mt-1 leading-relaxed">
                    النسبة المئوية التي تقتطعها المنصة كعمولة تشغيلية من قيمة كل طلب مكتمل ومسلّم. تظهر هذه النسبة تلقائياً في تقارير المبيعات والأرباح.
                </p>
            </div>
        </div>

        <form wire:submit.prevent="saveSettings" class="space-y-4">
            <div>
                <x-input-label for="commission" value="نسبة العمولة الافتراضية (%)" />
                <div class="relative mt-1 max-w-xs">
                    <x-text-input
                        id="commission"
                        type="number"
                        step="0.5"
                        min="0"
                        max="100"
                        wire:model.defer="commissionPercentage"
                        class="w-full pe-8"
                    />
                    <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none text-sufra-muted text-14 font-medium">
                        %
                    </div>
                </div>
                <x-input-error :messages="$errors->get('commissionPercentage')" class="mt-1" />
                <p class="text-14 text-sufra-muted mt-1.5">
                    القيمة الافتراضية المقررة هي <strong>10%</strong>. يمكنك تعديلها في أي وقت وستنعكس فورياً على الحسابات.
                </p>
            </div>

            <div class="pt-3 border-t border-sufra-border flex items-center justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded border border-transparent bg-sufra-primary text-sufra-surface text-14 font-medium hover:bg-sufra-primary/90 transition-colors shadow-none"
                >
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>حفظ التعديلات</span>
                </button>
            </div>
        </form>
    </div>
</div>

