@props([
    'src' => null,
    'initials' => null,
    'size' => 'md',          // xs | sm | md | lg | xl
    'status' => null,        // online | offline | busy | away
    'rounded' => 'full',     // full | lg
])

@php
    $sizes = [
        'xs' => 'size-6 text-xs',
        'sm' => 'size-8 text-xs',
        'md' => 'size-10 text-sm',
        'lg' => 'size-12 text-base',
        'xl' => 'size-16 text-lg',
    ];

    $statusColor = [
        'online' => 'bg-green-500',
        'offline' => 'bg-gray-400',
        'busy' => 'bg-red-500',
        'away' => 'bg-yellow-500',
    ][$status] ?? null;
@endphp

<span class="relative inline-flex shrink-0">
    @if($src)
        <img
            src="{{ $src }}"
            alt="Avatar"
            {{ $attributes->merge(['class' => "{$sizes[$size]} rounded-{$rounded} object-cover"]) }}
        >
    @else
        <span {{ $attributes->merge(['class' =>
            "{$sizes[$size]} inline-flex items-center justify-center rounded-{$rounded} bg-gray-200 font-semibold text-gray-600"
        ]) }}>
            {{ $initials }}
        </span>
    @endif

    @if($statusColor)
        <span class="absolute bottom-0 inset-e-0 block size-2.5 rounded-full ring-2 ring-white {{ $statusColor }}"></span>
    @endif
</span>
