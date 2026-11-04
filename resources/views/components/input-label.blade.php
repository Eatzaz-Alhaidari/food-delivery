@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-14 text-sufra-text mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
