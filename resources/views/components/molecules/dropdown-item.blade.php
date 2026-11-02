@props(['href' => '#'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex items-center gap-x-3 py-2 px-3 text-sm text-ink-700 hover:bg-surface-100 rounded-md mx-1']) }}>
    {{ $slot }}
</a>
