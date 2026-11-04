@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-sufra-surface'])

@php
$alignmentClasses = match ($align) {
    'left' => 'origin-top-left left-0',
    'top' => 'origin-top',
    default => 'origin-top-right right-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-1 {{ $width }} rounded border border-sufra-border bg-sufra-surface shadow-none {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
