{{--
    Baris item di popover messages/notifications.
    Isi salah satu: (avatarSrc + avatarFallback) ATAU (icon + iconVariant)
--}}
@props([
    'href' => '#',
    'title',
    'description',
    'meta',
    'avatarSrc' => null,
    'avatarFallback' => '',
    'icon' => null,
    'iconVariant' => 'primary',
    'unread' => false,
])

<a href="{{ $href }}" class="media @if($unread) media--unread @endif items-start">
    <div class="media__figure mt-1">
        @if($icon)
            <x-ui.icon-box :name="$icon" :variant="$iconVariant" />
        @else
            <x-ui.avatar :src="$avatarSrc" :fallback="$avatarFallback" size="sm" />
        @endif
    </div>
    <div class="media__content">
        <div class="media__title">{{ $title }}</div>
        <div class="media__description">{{ $description }}</div>
        <div class="media__meta">{{ $meta }}</div>
    </div>
</a>
