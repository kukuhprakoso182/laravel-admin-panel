@props([
    'variant' => 'solid',
    'size' => 'md',
    'color' => 'blue',
    'pill' => false,
    'loading' => false,
    'loadingWhen' => null,
    'type' => 'button',
    'href' => null,
])

@php
    $sizes = [
        'sm' => 'py-2 px-3 text-sm',
        'md' => 'py-2.5 px-4 text-sm',
        'lg' => 'py-3 px-5 text-sm',
    ];

    // Setiap kombinasi variant+color ditulis LENGKAP (literal) agar terdeteksi Tailwind.
    // Tambahkan warna baru di sini kalau perlu — jangan pakai interpolasi "{$color}".
    $variantColorMap = [
        'solid' => [
            'blue'   => 'bg-blue-600 border border-transparent text-white hover:bg-blue-700 focus:bg-blue-700',
            'red'    => 'bg-red-600 border border-transparent text-white hover:bg-red-700 focus:bg-red-700',
            'green'  => 'bg-green-600 border border-transparent text-white hover:bg-green-700 focus:bg-green-700',
            'gray'   => 'bg-gray-600 border border-transparent text-white hover:bg-gray-700 focus:bg-gray-700',
            'amber'  => 'bg-amber-500 border border-transparent text-white hover:bg-amber-600 focus:bg-amber-600',
            'indigo' => 'bg-indigo-600 border border-transparent text-white hover:bg-indigo-700 focus:bg-indigo-700',
            'teal'   => 'bg-teal-600 border border-transparent text-white hover:bg-teal-700 focus:bg-teal-700',
        ],
        'outline' => [
            'blue'   => 'bg-transparent border border-gray-300 text-gray-700 hover:border-blue-500 hover:text-blue-600 focus:border-blue-500 focus:text-blue-600',
            'red'    => 'bg-transparent border border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-600 focus:border-red-500 focus:text-red-600',
            'green'  => 'bg-transparent border border-gray-300 text-gray-700 hover:border-green-500 hover:text-green-600 focus:border-green-500 focus:text-green-600',
            'gray'   => 'bg-transparent border border-gray-300 text-gray-700 hover:border-gray-500 hover:text-gray-600 focus:border-gray-500 focus:text-gray-600',
            'amber'  => 'bg-transparent border border-gray-300 text-gray-700 hover:border-amber-500 hover:text-amber-600 focus:border-amber-500 focus:text-amber-600',
            'indigo' => 'bg-transparent border border-gray-300 text-gray-700 hover:border-indigo-500 hover:text-indigo-600 focus:border-indigo-500 focus:text-indigo-600',
            'teal'   => 'bg-transparent border border-gray-300 text-gray-700 hover:border-teal-500 hover:text-teal-600 focus:border-teal-500 focus:text-teal-600',
        ],
        'ghost' => [
            'blue'   => 'bg-transparent border border-transparent text-blue-600 hover:bg-blue-50 focus:bg-blue-50',
            'red'    => 'bg-transparent border border-transparent text-red-600 hover:bg-red-50 focus:bg-red-50',
            'green'  => 'bg-transparent border border-transparent text-green-600 hover:bg-green-50 focus:bg-green-50',
            'gray'   => 'bg-transparent border border-transparent text-gray-600 hover:bg-gray-50 focus:bg-gray-50',
            'amber'  => 'bg-transparent border border-transparent text-amber-600 hover:bg-amber-50 focus:bg-amber-50',
            'indigo' => 'bg-transparent border border-transparent text-indigo-600 hover:bg-indigo-50 focus:bg-indigo-50',
            'teal'   => 'bg-transparent border border-transparent text-teal-600 hover:bg-teal-50 focus:bg-teal-50',
        ],
        'soft' => [
            'blue'   => 'bg-blue-50 border border-transparent text-blue-700 hover:bg-blue-100 focus:bg-blue-100',
            'red'    => 'bg-red-50 border border-transparent text-red-700 hover:bg-red-100 focus:bg-red-100',
            'green'  => 'bg-green-50 border border-transparent text-green-700 hover:bg-green-100 focus:bg-green-100',
            'gray'   => 'bg-gray-50 border border-transparent text-gray-700 hover:bg-gray-100 focus:bg-gray-100',
            'amber'  => 'bg-amber-50 border border-transparent text-amber-700 hover:bg-amber-100 focus:bg-amber-100',
            'indigo' => 'bg-indigo-50 border border-transparent text-indigo-700 hover:bg-indigo-100 focus:bg-indigo-100',
            'teal'   => 'bg-teal-50 border border-transparent text-teal-700 hover:bg-teal-100 focus:bg-teal-100',
        ],
        'link' => [
            'blue'   => 'bg-transparent border border-transparent text-blue-600 hover:text-blue-700 underline-offset-2 hover:underline p-0!',
            'red'    => 'bg-transparent border border-transparent text-red-600 hover:text-red-700 underline-offset-2 hover:underline p-0!',
            'green'  => 'bg-transparent border border-transparent text-green-600 hover:text-green-700 underline-offset-2 hover:underline p-0!',
            'gray'   => 'bg-transparent border border-transparent text-gray-600 hover:text-gray-700 underline-offset-2 hover:underline p-0!',
            'amber'  => 'bg-transparent border border-transparent text-amber-600 hover:text-amber-700 underline-offset-2 hover:underline p-0!',
            'indigo' => 'bg-transparent border border-transparent text-indigo-600 hover:text-indigo-700 underline-offset-2 hover:underline p-0!',
            'teal'   => 'bg-transparent border border-transparent text-teal-600 hover:text-teal-700 underline-offset-2 hover:underline p-0!',
        ],
        // "white" tidak tergantung $color sama sekali, jadi aman tanpa map
        'white' => 'bg-white border border-gray-200 text-gray-700 shadow-sm hover:bg-gray-50 focus:bg-gray-50',
    ];

    $variantClasses = $variant === 'white'
        ? $variantColorMap['white']
        : ($variantColorMap[$variant][$color] ?? $variantColorMap['solid']['blue']);

    $classes = ($variant !== 'link' ? $sizes[$size] . ' ' : '') .
        'inline-flex items-center justify-center gap-x-2 font-medium rounded-lg my-3
         focus:outline-none disabled:opacity-50 disabled:pointer-events-none transition-colors cursor-pointer hover:drop-shadow-xl ' .
        $variantClasses .
        ($pill ? ' rounded-full' : '');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        @if($loadingWhen)
            x-bind:disabled="{{ $loadingWhen }}"
        @endif
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if($loadingWhen)
            <span x-show="{{ $loadingWhen }}" x-cloak
                  class="animate-spin inline-block size-4 border-2 border-current border-t-transparent rounded-full"
                  role="status" aria-label="loading"></span>
        @elseif($loading)
            <span class="animate-spin inline-block size-4 border-2 border-current border-t-transparent rounded-full" role="status" aria-label="loading"></span>
        @endif
        {{ $slot }}
    </button>
@endif
