@props([
    'name' => '',
    'size' => null,
])

{{-- مكوّن أيقونة Lucide الموحدة --}}
<i data-lucide="{{ $name }}" {{ $attributes->merge(['class' => 'inline-block align-middle ' . ($size ? "w-{$size} h-{$size}" : 'w-5 h-5')]) }}></i>

