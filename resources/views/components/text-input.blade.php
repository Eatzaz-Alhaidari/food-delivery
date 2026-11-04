@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-sufra-surface border border-sufra-border text-sufra-text placeholder-sufra-muted text-16 rounded px-3.5 py-2.5 focus:border-sufra-primary focus:ring-1 focus:ring-sufra-primary outline-none transition-colors duration-150 shadow-none']) }}>
