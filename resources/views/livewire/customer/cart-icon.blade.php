<button
    type="button"
    wire:click="openDrawer"
    class="relative inline-flex items-center justify-center p-2 rounded border border-sufra-border bg-sufra-surface hover:bg-sufra-bg text-sufra-text transition-colors duration-150 focus:outline-none"
    title="سلة الطلبات"
    aria-label="سلة الطلبات ({{ $count }} أصناف)"
>
    <i data-lucide="shopping-bag" class="w-5 h-5 text-sufra-text"></i>
    
    @if ($count > 0)
        <span class="absolute -top-1.5 -end-1.5 min-w-[20px] h-5 px-1 rounded bg-sufra-primary text-sufra-surface text-14 font-semibold flex items-center justify-center border border-sufra-surface">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</button>

