@props([
    'title' => 'لا توجد عناصر لعرضها',
    'description' => null,
    'icon' => 'inbox',
])

{{-- مكوّن الحالة الفارغة بروح ورق المنيو المطبوع والمساحات الهادئة بدون زخرفة --}}
<div {{ $attributes->merge(['class' => 'bg-sufra-surface border border-sufra-border rounded p-8 sm:p-12 text-center flex flex-col items-center justify-center shadow-none']) }}>
    {{-- أيقونة Lucide الموحدة بدون إيموجي --}}
    <div class="w-12 h-12 rounded border border-sufra-border bg-sufra-bg flex items-center justify-center text-sufra-muted mb-4">
        <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
    </div>

    {{-- عنوان الحالة الفارغة بخط Noto Kufi بحجم 20px --}}
    <h3 class="font-heading font-semibold text-20 text-sufra-text">
        {{ $title }}
    </h3>

    {{-- الوصف بخط IBM Plex بحجم 14px --}}
    @if ($description)
        <p class="text-14 text-sufra-muted mt-2 max-w-md mx-auto leading-relaxed">
            {{ $description }}
        </p>
    @elseif ($slot->isNotEmpty())
        <div class="text-14 text-sufra-muted mt-2 max-w-md mx-auto leading-relaxed">
            {{ $slot }}
        </div>
    @endif

    {{-- أزرار أو إجراءات اختيارية --}}
    @isset($action)
        <div class="mt-5">
            {{ $action }}
        </div>
    @endisset
</div>

