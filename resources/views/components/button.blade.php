@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconPosition' => 'start',
])

@php
// تحديد ألوان الزر بحسب المتغير المعتمد في DESIGN.md
$variantClasses = match ($variant) {
    'secondary' => 'bg-sufra-surface hover:bg-sufra-bg text-sufra-text border-sufra-border focus:ring-sufra-primary',
    'danger', 'accent' => 'bg-sufra-accent hover:bg-sufra-accent-hover text-sufra-surface border-sufra-accent focus:ring-sufra-accent',
    'outline' => 'bg-transparent hover:bg-sufra-surface text-sufra-primary border-sufra-primary focus:ring-sufra-primary',
    default => 'bg-sufra-primary hover:bg-sufra-primary-hover text-sufra-surface border-sufra-primary focus:ring-sufra-primary',
};

// تحديد الحجم بحسب أحجام النصوص المسموحة في DESIGN.md (14 و 16 فقط للأزرار)
$sizeClasses = match ($size) {
    'sm', '14' => 'text-14 px-3 py-1.5 gap-1.5',
    default => 'text-16 px-4 py-2.5 gap-2',
};

$baseClasses = 'inline-flex items-center justify-center font-medium rounded border transition-colors duration-150 focus:outline-none focus:ring-1 focus:ring-offset-0 disabled:opacity-50 select-none shadow-none cursor-pointer';
$allClasses = "{$baseClasses} {$variantClasses} {$sizeClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $allClasses]) }}>
        @if ($icon && $iconPosition === 'start')
            <i data-lucide="{{ $icon }}" class="w-4 h-4 flex-shrink-0"></i>
        @endif
        <span>{{ $slot }}</span>
        @if ($icon && $iconPosition === 'end')
            <i data-lucide="{{ $icon }}" class="w-4 h-4 flex-shrink-0"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $allClasses]) }}>
        @if ($icon && $iconPosition === 'start')
            <i data-lucide="{{ $icon }}" class="w-4 h-4 flex-shrink-0"></i>
        @endif
        <span>{{ $slot }}</span>
        @if ($icon && $iconPosition === 'end')
            <i data-lucide="{{ $icon }}" class="w-4 h-4 flex-shrink-0"></i>
        @endif
    </button>
@endif

