@props([
    'src' => null,
    'name' => null,
    'initials' => null,
    'size' => 'md',
    'shape' => 'full',
    'color' => null,
    'fit' => 'cover',   // cover (avatar/foto, boleh crop) | contain (logo/icon, tidak boleh crop)
])

@php
    $sizes = [
        'xs' => 'size-6 text-xs',
        'sm' => 'size-8 text-xs',
        'md' => 'size-10 text-sm',
        'lg' => 'size-12 text-base',
        'xl' => 'size-16 text-lg',
    ];

    $shapes = [
        'full' => 'rounded-full',
        'lg' => 'rounded-lg',
        'md' => 'rounded-md',
    ];

    $resolvedInitials = $initials;

    if (!$resolvedInitials && $name) {
        $words = array_filter(preg_split('/\s+/', trim($name)));

        if (count($words) >= 2) {
            $first = mb_substr($words[0], 0, 1);
            $last = mb_substr(end($words), 0, 1);
            $resolvedInitials = mb_strtoupper($first . $last);
        } elseif (count($words) === 1) {
            $resolvedInitials = mb_strtoupper(mb_substr($words[0], 0, 2));
        }
    }

    $resolvedInitials = $resolvedInitials ?: '?';

    $palette = [
        'red' => 'bg-red-100 text-red-700',
        'orange' => 'bg-orange-100 text-orange-700',
        'yellow' => 'bg-yellow-100 text-yellow-700',
        'green' => 'bg-teal-100 text-teal-700',
        'blue' => 'bg-blue-100 text-blue-700',
        'indigo' => 'bg-indigo-100 text-indigo-700',
        'purple' => 'bg-purple-100 text-purple-700',
        'pink' => 'bg-pink-100 text-pink-700',
        'gray' => 'bg-gray-100 text-gray-700',
        'teal' => 'bg-teal-100 text-teal-700',
        'slate' => 'bg-slate-200 text-slate-700',
    ];

    if ($color && isset($palette[$color])) {
        $colorClass = $palette[$color];
    } elseif ($resolvedInitials !== '?') {
        $hashSource = $name ?: $resolvedInitials;
        $keys = array_keys($palette);
        $hash = crc32($hashSource);
        $colorClass = $palette[$keys[$hash % count($keys)]];
    } else {
        $colorClass = $palette['gray'];
    }

    // object-contain vs object-cover ditulis literal (bukan interpolasi) agar Tailwind bisa scan
    $fitClass = $fit === 'contain' ? 'object-contain' : 'object-cover';
@endphp

<span
    {{ $attributes->merge(['class' =>
        "{$sizes[$size]} {$shapes[$shape]} relative inline-flex items-center justify-center shrink-0 font-semibold overflow-hidden " .
        ($src ? '' : $colorClass)
    ]) }}
    @if($name) title="{{ $name }}" @endif
>
    @if($src)
        <img
            src="{{ $src }}"
            alt="{{ $name ?? $resolvedInitials }}"
            class="absolute inset-0 size-full {{ $fitClass }}"
        >
    @else
        {{ $resolvedInitials }}
    @endif
</span>
