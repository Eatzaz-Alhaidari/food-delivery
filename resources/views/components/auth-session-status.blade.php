@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'bg-sufra-surface border border-sufra-primary text-sufra-primary px-3.5 py-2.5 rounded text-14 flex items-center gap-2']) }}>
        <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
        <span>{{ $status }}</span>
    </div>
@endif
