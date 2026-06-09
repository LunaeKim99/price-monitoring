@props([
    'amount' => 0,
])

@php
    $formatted = number_format((float) $amount, 0, ',', '.');
    $color = 'blue';
    if ($amount > 50000) {
        $color = 'red';
    } elseif ($amount > 10000) {
        $color = 'yellow';
    }

    $bgClasses = [
        'blue' => 'bg-blue-100',
        'red' => 'bg-red-100',
        'yellow' => 'bg-yellow-100',
    ];
    $textClasses = [
        'blue' => 'text-blue-800',
        'red' => 'text-red-800',
        'yellow' => 'text-yellow-800',
    ];
    $bgClass = $bgClasses[$color] ?? 'bg-blue-100';
    $textClass = $textClasses[$color] ?? 'text-blue-800';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bgClass }} {{ $textClass }}">
    Rp {{ $formatted }}
</span>
