{{-- Colored icon container used inside a summary card. --}}
@props(['color' => 'gray', 'icon'])

@php
    $colorMap = [
        'blue' => 'bg-blue-50 text-blue-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
    ];
    $classes = $colorMap[$color] ?? 'bg-gray-50 text-gray-600';
@endphp

<span class="inline-flex items-center justify-center w-10 h-10 rounded-lg {{ $classes }}">
    @include('pages.dashboard.partials.icon', ['name' => $icon])
</span>
