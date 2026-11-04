<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-sufra-surface hover:bg-sufra-bg border border-sufra-border rounded text-14 font-medium text-sufra-text transition-colors duration-150 focus:outline-none focus:ring-1 focus:ring-sufra-primary disabled:opacity-50 select-none shadow-none']) }}>
    {{ $slot }}
</button>