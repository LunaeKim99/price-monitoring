@props([
    'title' => '',
    'icon' => null,
    'variant' => 'default',
])

@php
    $variantClasses = [
        'default' => 'bg-white border-gray-200',
        'warning' => 'bg-yellow-50 border-yellow-200',
        'danger' => 'bg-red-50 border-red-200',
    ][$variant] ?? 'bg-white border-gray-200';
@endphp

<div class="rounded-lg shadow-md p-6 {{ $variantClasses }} border">
    @if($title || $icon)
        <div class="flex items-center gap-2 mb-4">
            @if($icon)
                <span class="text-xl">{!! $icon !!}</span>
            @endif
            @if($title)
                <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
            @endif
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>
</div>
