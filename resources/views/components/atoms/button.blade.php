@props([
    'variant' => 'solid',
    'size' => 'md',
    'color' => 'primary',
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
    // 'primary' = warna tema (lihat @theme di resources/css/app.css); blue/teal/dst tetap tersedia sebagai warna bernama.
    // Tambahkan warna baru di sini kalau perlu — jangan pakai interpolasi "{$color}".
    $variantColorMap = [
        'solid' => [
            'primary' => 'bg-primary-600 border border-transparent text-white hover:bg-primary-700 focus:bg-primary-700',
            'blue'   => 'bg-blue-600 border border-transparent text-white hover:bg-blue-700 focus:bg-blue-700',
            'red'    => 'bg-red-600 border border-transparent text-white hover:bg-red-700 focus:bg-red-700',
            'green'  => 'bg-green-600 border border-transparent text-white hover:bg-green-700 focus:bg-green-700',
            'gray'   => 'bg-surface-600 border border-transparent text-white hover:bg-surface-700 focus:bg-surface-700',
            'amber'  => 'bg-amber-500 border border-transparent text-white hover:bg-amber-600 focus:bg-amber-600',
            'indigo' => 'bg-indigo-600 border border-transparent text-white hover:bg-indigo-700 focus:bg-indigo-700',
            'teal'   => 'bg-teal-600 border border-transparent text-white hover:bg-teal-700 focus:bg-teal-700',
        ],
        'outline' => [
            'primary' => 'bg-transparent border border-line-300 text-ink-700 hover:border-primary-500 hover:text-primary-600 focus:border-primary-500 focus:text-primary-600',
            'blue'   => 'bg-transparent border border-line-300 text-ink-700 hover:border-blue-500 hover:text-blue-600 focus:border-blue-500 focus:text-blue-600',
            'red'    => 'bg-transparent border border-line-300 text-ink-700 hover:border-red-500 hover:text-red-600 focus:border-red-500 focus:text-red-600',
            'green'  => 'bg-transparent border border-line-300 text-ink-700 hover:border-green-500 hover:text-green-600 focus:border-green-500 focus:text-green-600',
            'gray'   => 'bg-transparent border border-line-300 text-ink-700 hover:border-line-500 hover:text-ink-600 focus:border-line-500 focus:text-ink-600',
            'amber'  => 'bg-transparent border border-line-300 text-ink-700 hover:border-amber-500 hover:text-amber-600 focus:border-amber-500 focus:text-amber-600',
            'indigo' => 'bg-transparent border border-line-300 text-ink-700 hover:border-indigo-500 hover:text-indigo-600 focus:border-indigo-500 focus:text-indigo-600',
            'teal'   => 'bg-transparent border border-line-300 text-ink-700 hover:border-teal-500 hover:text-teal-600 focus:border-teal-500 focus:text-teal-600',
        ],
        'ghost' => [
            'primary' => 'bg-transparent border border-transparent text-primary-600 hover:bg-primary-50 focus:bg-primary-50',
            'blue'   => 'bg-transparent border border-transparent text-blue-600 hover:bg-blue-50 focus:bg-blue-50',
            'red'    => 'bg-transparent border border-transparent text-red-600 hover:bg-red-50 focus:bg-red-50',
            'green'  => 'bg-transparent border border-transparent text-green-600 hover:bg-green-50 focus:bg-green-50',
            'gray'   => 'bg-transparent border border-transparent text-ink-600 hover:bg-surface-50 focus:bg-surface-50',
            'amber'  => 'bg-transparent border border-transparent text-amber-600 hover:bg-amber-50 focus:bg-amber-50',
            'indigo' => 'bg-transparent border border-transparent text-indigo-600 hover:bg-indigo-50 focus:bg-indigo-50',
            'teal'   => 'bg-transparent border border-transparent text-teal-600 hover:bg-teal-50 focus:bg-teal-50',
        ],
        'soft' => [
            'primary' => 'bg-primary-50 border border-transparent text-primary-700 hover:bg-primary-100 focus:bg-primary-100',
            'blue'   => 'bg-blue-50 border border-transparent text-blue-700 hover:bg-blue-100 focus:bg-blue-100',
            'red'    => 'bg-red-50 border border-transparent text-red-700 hover:bg-red-100 focus:bg-red-100',
            'green'  => 'bg-green-50 border border-transparent text-green-700 hover:bg-green-100 focus:bg-green-100',
            'gray'   => 'bg-surface-50 border border-transparent text-ink-700 hover:bg-surface-100 focus:bg-surface-100',
            'amber'  => 'bg-amber-50 border border-transparent text-amber-700 hover:bg-amber-100 focus:bg-amber-100',
            'indigo' => 'bg-indigo-50 border border-transparent text-indigo-700 hover:bg-indigo-100 focus:bg-indigo-100',
            'teal'   => 'bg-teal-50 border border-transparent text-teal-700 hover:bg-teal-100 focus:bg-teal-100',
        ],
        'link' => [
            'primary' => 'bg-transparent border border-transparent text-primary-600 hover:text-primary-700 underline-offset-2 hover:underline p-0!',
            'blue'   => 'bg-transparent border border-transparent text-blue-600 hover:text-blue-700 underline-offset-2 hover:underline p-0!',
            'red'    => 'bg-transparent border border-transparent text-red-600 hover:text-red-700 underline-offset-2 hover:underline p-0!',
            'green'  => 'bg-transparent border border-transparent text-green-600 hover:text-green-700 underline-offset-2 hover:underline p-0!',
            'gray'   => 'bg-transparent border border-transparent text-ink-600 hover:text-ink-700 underline-offset-2 hover:underline p-0!',
            'amber'  => 'bg-transparent border border-transparent text-amber-600 hover:text-amber-700 underline-offset-2 hover:underline p-0!',
            'indigo' => 'bg-transparent border border-transparent text-indigo-600 hover:text-indigo-700 underline-offset-2 hover:underline p-0!',
            'teal'   => 'bg-transparent border border-transparent text-teal-600 hover:text-teal-700 underline-offset-2 hover:underline p-0!',
        ],
        // "white" tidak tergantung $color sama sekali, jadi aman tanpa map
        'white' => 'bg-white border border-line-200 text-ink-700 shadow-sm hover:bg-surface-50 focus:bg-surface-50',
    ];

    $variantClasses = $variant === 'white'
        ? $variantColorMap['white']
        : ($variantColorMap[$variant][$color] ?? $variantColorMap['solid']['primary']);

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
