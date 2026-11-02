@props([
    'value' => 'row.status',        // expression Alpine yang dievaluasi
    'trueValue' => "'active'",      // nilai pembanding untuk kondisi true (tetap string Alpine, quote manual)
    'trueLabel' => 'Aktif',
    'falseLabel' => 'Nonaktif',
    'trueColor' => 'green',
    'falseColor' => 'gray',
])

@php
    // Kombinasi ditulis LITERAL agar terdeteksi Tailwind (jangan interpolasi "bg-{$color}-100").
    $palette = [
        'gray' => 'bg-gray-100 text-gray-700',
        'primary' => 'bg-primary-100 text-primary-700',
        'blue' => 'bg-blue-100 text-blue-700',
        'red' => 'bg-red-100 text-red-700',
        'yellow' => 'bg-yellow-100 text-yellow-700',
        'green' => 'bg-green-100 text-green-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'indigo' => 'bg-indigo-100 text-indigo-700',
        'teal' => 'bg-teal-100 text-teal-700',
    ];

    // Blade resolve kelas lengkap di sini (server-side), bukan dirakit di JS
    $trueClass = $palette[$trueColor] ?? $palette['green'];
    $falseClass = $palette[$falseColor] ?? $palette['gray'];
@endphp

<span
    class="inline-flex items-center py-1 px-2.5 rounded-full text-xs font-medium"
    x-bind:class="{{ $value }} === {{ $trueValue }} ? '{{ $trueClass }}' : '{{ $falseClass }}'"
    x-text="{{ $value }} === {{ $trueValue }} ? '{{ $trueLabel }}' : '{{ $falseLabel }}'"
></span>
