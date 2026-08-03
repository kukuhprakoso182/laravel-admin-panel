@props(['label' => null])

<div>
    @if($label)
        <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $label }}</p>
    @endif
    <div class="space-y-1">
        {{ $slot }}
    </div>
</div>
