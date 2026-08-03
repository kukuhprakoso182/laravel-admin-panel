@props([
    'size' => 'md',   // sm | md | lg
    'color' => 'blue',
])

@php
    $sizes = ['sm' => 'size-4 border-2', 'md' => 'size-6 border-2', 'lg' => 'size-8 border-[3px]'];
@endphp

<span
    {{ $attributes->merge(['class' =>
        "animate-spin inline-block {$sizes[$size]} border-{$color}-600 border-t-transparent rounded-full"
    ]) }}
    role="status"
    aria-label="loading"
>
    <span class="sr-only">Loading...</span>
</span>
