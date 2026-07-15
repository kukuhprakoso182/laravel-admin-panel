{{--
    Pakai: <x-ui.badge variant="success" soft icon="arrow-up-line">11%</x-ui.badge>
--}}
@props(['variant' => 'primary', 'soft' => false, 'icon' => null])

<span class="badge @if($soft) badge--soft @endif badge--{{ $variant }}">
    @if($icon)
        <x-ui.icon :name="$icon" />
    @endif
    {{ $slot }}
</span>
