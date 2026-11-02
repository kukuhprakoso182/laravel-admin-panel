@props([
    'size' => 'md',   // sm | md | lg
    'color' => 'primary',   // primary | blue | gray | red | green | teal
])

@php
    $sizes = ['sm' => 'size-4 border-2', 'md' => 'size-6 border-2', 'lg' => 'size-8 border-[3px]'];

    // Literal agar terdeteksi Tailwind (jangan interpolasi "border-{$color}-600")
    $colors = [
        'primary' => 'border-primary-600',
        'blue' => 'border-blue-600',
        'gray' => 'border-gray-600',
        'red' => 'border-red-600',
        'green' => 'border-green-600',
        'teal' => 'border-teal-600',
    ];

    $colorClass = $colors[$color] ?? $colors['primary'];
@endphp

<span
    {{ $attributes->merge(['class' =>
        "animate-spin inline-block {$sizes[$size]} {$colorClass} border-t-transparent rounded-full"
    ]) }}
    role="status"
    aria-label="loading"
>
    <span class="sr-only">Loading...</span>
</span>
