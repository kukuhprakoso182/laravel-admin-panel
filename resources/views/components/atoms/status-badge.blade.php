@props([
    'value' => 'row.status',        // expression Alpine yang dievaluasi
    'trueValue' => "'active'",      // nilai pembanding untuk kondisi true (tetap string Alpine, quote manual)
    'trueLabel' => 'Aktif',
    'falseLabel' => 'Nonaktif',
    'trueColor' => 'green',
    'falseColor' => 'gray',
])

@php
    // Blade resolve kelas lengkap di sini (server-side), bukan dirakit di JS
    $trueClass = "bg-{$trueColor}-100 text-{$trueColor}-700";
    $falseClass = "bg-{$falseColor}-100 text-{$falseColor}-700";
@endphp

<span
    class="inline-flex items-center py-1 px-2.5 rounded-full text-xs font-medium"
    x-bind:class="{{ $value }} === {{ $trueValue }} ? '{{ $trueClass }}' : '{{ $falseClass }}'"
    x-text="{{ $value }} === {{ $trueValue }} ? '{{ $trueLabel }}' : '{{ $falseLabel }}'"
></span>
