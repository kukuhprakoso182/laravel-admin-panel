@props([
    'variant' => 'soft',   // solid | soft | outline | white
    'color' => 'gray',     // gray | primary | blue | red | yellow | green | amber | indigo | teal
    'pill' => true,
    'dot' => false,
])

@php
    // Setiap kombinasi ditulis LITERAL agar terdeteksi scanner Tailwind.
    // Jangan pakai interpolasi seperti "bg-{$color}-600": class-nya tidak akan ter-generate.
    // 'primary' mengikuti warna tema (lihat @theme di resources/css/app.css).
    $variants = [
        'solid' => [
            'gray' => 'bg-gray-600 text-white',
            'primary' => 'bg-primary-600 text-white',
            'blue' => 'bg-blue-600 text-white',
            'red' => 'bg-red-600 text-white',
            'yellow' => 'bg-yellow-600 text-white',
            'green' => 'bg-green-600 text-white',
            'amber' => 'bg-amber-600 text-white',
            'indigo' => 'bg-indigo-600 text-white',
            'teal' => 'bg-teal-600 text-white',
        ],
        'soft' => [
            'gray' => 'bg-gray-100 text-gray-800',
            'primary' => 'bg-primary-100 text-primary-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'red' => 'bg-red-100 text-red-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'green' => 'bg-green-100 text-green-800',
            'amber' => 'bg-amber-100 text-amber-800',
            'indigo' => 'bg-indigo-100 text-indigo-800',
            'teal' => 'bg-teal-100 text-teal-800',
        ],
        'outline' => [
            'gray' => 'bg-transparent border border-gray-500 text-gray-600',
            'primary' => 'bg-transparent border border-primary-500 text-primary-600',
            'blue' => 'bg-transparent border border-blue-500 text-blue-600',
            'red' => 'bg-transparent border border-red-500 text-red-600',
            'yellow' => 'bg-transparent border border-yellow-500 text-yellow-600',
            'green' => 'bg-transparent border border-green-500 text-green-600',
            'amber' => 'bg-transparent border border-amber-500 text-amber-600',
            'indigo' => 'bg-transparent border border-indigo-500 text-indigo-600',
            'teal' => 'bg-transparent border border-teal-500 text-teal-600',
        ],
    ];

    $dots = [
        'gray' => 'bg-gray-500',
        'primary' => 'bg-primary-500',
        'blue' => 'bg-blue-500',
        'red' => 'bg-red-500',
        'yellow' => 'bg-yellow-500',
        'green' => 'bg-green-500',
        'amber' => 'bg-amber-500',
        'indigo' => 'bg-indigo-500',
        'teal' => 'bg-teal-500',
    ];

    $variantClasses = $variant === 'white'
        ? 'bg-white border border-gray-200 text-gray-700 shadow-sm'
        : ($variants[$variant][$color] ?? $variants['soft']['gray']);
@endphp

<span {{ $attributes->merge(['class' =>
    'inline-flex items-center gap-x-1.5 py-1 px-2.5 text-xs font-medium ' .
    ($pill ? 'rounded-full' : 'rounded-md') . ' ' .
    $variantClasses
]) }}>
    @if($dot)
        <span class="size-1.5 rounded-full {{ $dots[$color] ?? $dots['gray'] }}"></span>
    @endif
    {{ $slot }}
</span>
