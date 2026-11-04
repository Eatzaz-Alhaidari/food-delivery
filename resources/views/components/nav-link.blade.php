@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-2 pt-1 border-b-2 border-sufra-primary text-14 font-medium text-sufra-text focus:outline-none transition-colors duration-150'
            : 'inline-flex items-center px-2 pt-1 border-b-2 border-transparent text-14 font-medium text-sufra-muted hover:text-sufra-text hover:border-sufra-border focus:outline-none transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
