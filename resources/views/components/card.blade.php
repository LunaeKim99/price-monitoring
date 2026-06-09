@props([
    'title' => '',
    'icon' => null,
    'variant' => 'default',
])

@php
    $variantClasses = [
        'default' => 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700',
        'warning' => 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-700',
        'danger' => 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-700',
    ][$variant] ?? 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700';
@endphp

<div class="rounded-lg shadow-md p-6 {{ $variantClasses }} border">
    @if($title || $icon)
        <div class="flex items-center gap-2 mb-4">
            @if($icon)
                {{-- WARNING: Only pass trusted SVG. No user input allowed. --}}
                <span class="text-xl">{!! $icon !!}</span>
            @endif
            @if($title)
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $title }}</h3>
            @endif
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>
</div>
