@props([
    'variant' => 'soft',   // solid | soft | outline | white
    'color' => 'gray',     // gray | blue | red | yellow | green
    'pill' => true,
    'dot' => false,
])

@php
    $variants = [
        'solid' => "bg-{$color}-600 text-white",
        'soft' => "bg-{$color}-100 text-{$color}-800",
        'outline' => "bg-transparent border border-{$color}-500 text-{$color}-600",
        'white' => "bg-white border border-gray-200 text-gray-700 shadow-sm",
    ];
@endphp

<span {{ $attributes->merge(['class' =>
    'inline-flex items-center gap-x-1.5 py-1 px-2.5 text-xs font-medium ' .
    ($pill ? 'rounded-full' : 'rounded-md') . ' ' .
    ($variants[$variant] ?? $variants['soft'])
]) }}>
    @if($dot)
        <span class="size-1.5 rounded-full bg-{{ $color }}-500"></span>
    @endif
    {{ $slot }}
</span>
