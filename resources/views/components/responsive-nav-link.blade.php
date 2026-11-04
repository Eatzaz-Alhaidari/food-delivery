@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full pe-3 ps-4 py-2 border-r-4 border-sufra-primary text-start text-16 font-medium text-sufra-text bg-sufra-surface focus:outline-none transition-colors duration-150'
            : 'block w-full pe-3 ps-4 py-2 border-r-4 border-transparent text-start text-16 font-medium text-sufra-muted hover:text-sufra-text hover:bg-sufra-surface focus:outline-none transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
