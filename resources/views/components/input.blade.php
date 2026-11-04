@props([
    'disabled' => false,
    'label' => null,
    'error' => null,
    'icon' => null,
    'id' => null,
])

@php
$hasError = !empty($error);
$inputClasses = 'w-full bg-sufra-surface text-sufra-text placeholder-sufra-muted text-16 rounded px-3.5 py-2.5 transition-colors duration-150 outline-none shadow-none';

if ($hasError) {
    $inputClasses .= ' border border-sufra-accent focus:border-sufra-accent focus:ring-1 focus:ring-sufra-accent';
} else {
    $inputClasses .= ' border border-sufra-border focus:border-sufra-primary focus:ring-1 focus:ring-sufra-primary';
}

if ($icon) {
    $inputClasses .= ' ps-10';
}
@endphp

<div class="w-full">
    @if ($label)
        <label @if($id) for="{{ $id }}" @endif class="block font-medium text-14 text-sufra-text mb-1.5">
            {{ $label }}
            @if ($attributes->has('required'))
                <span class="text-sufra-accent">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if ($icon)
            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-sufra-muted">
                <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
            </div>
        @endif

        <input
            @if($id) id="{{ $id }}" @endif
            @disabled($disabled)
            {{ $attributes->merge(['class' => $inputClasses]) }}
        />
    </div>

    @if ($hasError)
        <p class="mt-1.5 text-14 text-sufra-accent flex items-center gap-1.5">
            <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
            <span>{{ $error }}</span>
        </p>
    @endif
</div>

