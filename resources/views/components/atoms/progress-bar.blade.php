{{-- Generic horizontal progress/percentage bar. --}}
@props(['percentage' => 0, 'color' => 'bg-purple-500', 'track' => 'bg-surface-100', 'height' => 'h-2'])

<div class="w-full {{ $track }} rounded-full {{ $height }}">
    <div class="{{ $color }} {{ $height }} rounded-full" style="width: {{ $percentage }}%"></div>
</div>
