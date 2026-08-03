@props([
    'for' => null,
    'required' => false,
    'size' => 'sm',   // xs | sm | base | lg
])

@php
    // Ditulis literal (bukan interpolasi "text-{$size}") agar terdeteksi Tailwind scanner
    $sizes = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'base' => 'text-base',
        'lg' => 'text-lg',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['sm'];
@endphp

<label
    for="{{ $for }}"
    {{ $attributes->merge(['class' => "block {$sizeClass} font-medium mb-2 text-gray-700"]) }}
>
    {{ $slot }}
    @if($required)
        <span class="text-red-500">*</span>
    @endif
</label>
