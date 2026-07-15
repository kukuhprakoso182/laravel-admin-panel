{{--
    Pakai:
    <x-ui.button variant="primary">Simpan</x-ui.button>
    <x-ui.button variant="neutral" ghost href="{{ route('orders.index') }}">View all</x-ui.button>
    <x-ui.button variant="neutral" ghost icon-only aria-label="Toggle sidebar"><x-ui.icon name="menu-line" /></x-ui.button>
--}}
@props([
    'variant' => 'primary',
    'ghost' => false,
    'outline' => false,
    'size' => null,
    'iconOnly' => false,
    'href' => null,
])

@php
$classes = collect(['button'])
    ->when($ghost, fn ($c) => $c->push('button--ghost'))
    ->when($outline, fn ($c) => $c->push('button--outline'))
    ->push('button--' . $variant)
    ->when($size, fn ($c) => $c->push('button--' . $size))
    ->when($iconOnly, fn ($c) => $c->push('button--icon-only'))
    ->implode(' ');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
