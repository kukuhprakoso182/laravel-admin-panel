{{--
    Pakai: <x-ui.icon-box name="alarm-warning-line" variant="danger" size="lg" />
    variant: primary | success | warning | danger | neutral (sesuai token warna Stisla)
--}}
@props(['name', 'variant' => 'primary', 'size' => null])

<span class="icon-box icon-box--{{ $variant }} @if($size) icon-box--{{ $size }} @endif">
    <x-ui.icon :name="$name" />
</span>
