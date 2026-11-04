<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-sufra-primary hover:bg-sufra-primary-hover border border-sufra-primary rounded text-14 font-medium text-sufra-surface transition-colors duration-150 focus:outline-none focus:ring-1 focus:ring-sufra-primary disabled:opacity-50 select-none shadow-none']) }}>
    {{ $slot }}
</button>
