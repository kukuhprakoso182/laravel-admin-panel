{{-- ▲/▼ percentage-change indicator. Renders nothing when $value is null. --}}
@props(['value'])

@if (! is_null($value))
    <p class="mt-3 text-xs font-medium {{ $value >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
        {{ $value >= 0 ? '▲' : '▼' }} {{ abs($value) }}%
        <span class="text-gray-400 font-normal">vs bulan lalu</span>
    </p>
@endif
