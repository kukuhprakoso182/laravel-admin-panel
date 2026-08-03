@props([
    'href' => '#',
    'active' => false,
    'badge' => null,
])

<a href="{{ $href }}"
   {{ $attributes->merge(['class' =>
        'flex items-center justify-between gap-x-3 px-1 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer ' .
        ($active
            ? 'bg-white text-teal-600 shadow-md'
            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900')
   ]) }}
>
    <span class="flex items-center gap-x-3">
        {{ $slot }}
    </span>

    @if($badge)
        <span class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full text-xs font-semibold bg-blue-600 text-white">
            {{ $badge }}
        </span>
    @endif
</a>
