@props(['nameVar' => 'user.name', 'size' => 'sm'])

@php
    $sizes = ['xs' => 'size-6 text-xs', 'sm' => 'size-8 text-xs', 'md' => 'size-10 text-sm'];
@endphp

<span
    class="{{ $sizes[$size] ?? $sizes['sm'] }} rounded-full inline-flex items-center justify-center shrink-0 font-semibold bg-primary-100 text-primary-700"
    x-text="({{ $nameVar }} || '?').trim().split(/\s+/).map(w => w[0]).slice(0,2).join('').toUpperCase()"
></span>
