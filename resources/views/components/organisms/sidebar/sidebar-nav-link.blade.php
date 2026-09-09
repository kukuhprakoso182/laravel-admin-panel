@props([
    'href' => '#',
    'active' => false,
    'badge' => null,
])

<a href="{{ $href }}"
   {{ $attributes->merge(['class' =>
        'flex items-center justify-between gap-x-3 px-1 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer my-2 ' .
        ($active
            ? 'bg-teal-600 text-white shadow-md'
            : 'text-gray-800 hover:bg-gray-200 hover:shadow-md hover:text-gray-900')
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
