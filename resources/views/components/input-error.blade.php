@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-14 text-sufra-accent space-y-1 mt-1.5']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1.5">
                <i data-lucide="alert-circle" class="w-4 h-4 inline-block flex-shrink-0"></i>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif
