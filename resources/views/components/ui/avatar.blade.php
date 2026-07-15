{{--
    Pakai: <x-ui.avatar :src="$user->avatar_url" fallback="SG" size="sm" />
    size: sm | md | lg (sesuaikan dengan kelas yang tersedia di style.css Meridian)
--}}
@props(['src' => null, 'fallback' => '', 'size' => 'sm', 'circle' => true])

<span class="avatar avatar--{{ $size }} @if($circle) avatar--circle @endif" data-stisla-avatar>
    @if($src)
        <img class="avatar__image" src="{{ $src }}" alt="" />
    @endif
    <span class="avatar__fallback">{{ $fallback }}</span>
</span>
