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
        'blue' => 'bg-blue-100 dark:bg-blue-900/40',
        'red' => 'bg-red-100 dark:bg-red-900/40',
        'yellow' => 'bg-yellow-100 dark:bg-yellow-900/40',
    ];
    $textClasses = [
        'blue' => 'text-blue-800 dark:text-blue-300',
        'red' => 'text-red-800 dark:text-red-300',
        'yellow' => 'text-yellow-800 dark:text-yellow-300',
    ];
    $bgClass = $bgClasses[$color] ?? 'bg-blue-100 dark:bg-blue-900/40';
    $textClass = $textClasses[$color] ?? 'text-blue-800 dark:text-blue-300';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bgClass }} {{ $textClass }}">
    Rp {{ $formatted }}
</span>
