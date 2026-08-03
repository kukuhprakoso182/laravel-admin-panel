@props([
    'size' => 'sm',        // xs | sm | base | lg
    'weight' => 'normal',  // normal | medium | semibold | bold
    'color' => 'gray',     // gray | blue | red | green | amber
    'tone' => 'default',   // default | muted | subtle
    'uppercase' => false,
    'truncate' => false,
])

@php
    $sizes = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'base' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-xl',
    ];

    $weights = [
        'normal' => 'font-normal',
        'medium' => 'font-medium',
        'semibold' => 'font-semibold',
        'bold' => 'font-bold',
    ];

    // Kombinasi color+tone ditulis literal (bukan interpolasi) supaya terdeteksi Tailwind scanner
    $colorToneMap = [
        'gray'  => ['default' => 'text-gray-700', 'muted' => 'text-gray-500', 'subtle' => 'text-gray-400'],
        'blue'  => ['default' => 'text-blue-700', 'muted' => 'text-blue-500', 'subtle' => 'text-blue-400'],
        'red'   => ['default' => 'text-red-700', 'muted' => 'text-red-500', 'subtle' => 'text-red-400'],
        'green' => ['default' => 'text-green-700', 'muted' => 'text-green-500', 'subtle' => 'text-green-400'],
        'amber' => ['default' => 'text-amber-700', 'muted' => 'text-amber-500', 'subtle' => 'text-amber-400'],
        'teal'  => ['default' => 'text-teal-700', 'muted' => 'text-teal-500', 'subtle' => 'text-teal-400'],
    ];

    $classes = trim(
        ($sizes[$size] ?? $sizes['sm']) . ' ' .
        ($weights[$weight] ?? $weights['normal']) . ' ' .
        ($colorToneMap[$color][$tone] ?? $colorToneMap['gray']['default']) .
        ($uppercase ? ' uppercase tracking-wide' : '') .
        ($truncate ? ' truncate block' : '')
    );
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
