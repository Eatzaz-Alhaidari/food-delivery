@props([
    'variant' => 'accent',
    'icon' => null,
])

@php
// شارة معتمدة وفق DESIGN.md بحجم خط 14px وحد رفيع 1px وزوايا 6px وبدون تدرجات
$variantClasses = match ($variant) {
    'primary', 'olive' => 'bg-sufra-surface border-sufra-primary text-sufra-primary',
    'solid-olive' => 'bg-sufra-primary border-sufra-primary text-sufra-surface',
    'solid-accent' => 'bg-sufra-accent border-sufra-accent text-sufra-surface',
    'neutral' => 'bg-sufra-surface border-sufra-border text-sufra-muted',
    default => 'bg-sufra-surface border-sufra-accent text-sufra-accent',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2.5 py-0.5 rounded border text-14 font-medium {$variantClasses} select-none shadow-none"]) }}>
    @if ($icon)
        <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5 flex-shrink-0"></i>
    @endif
    <span>{{ $slot }}</span>
</span>

