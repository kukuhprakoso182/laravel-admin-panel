{{--
    $notifications = collection/array of:
    ['href', 'title', 'description', 'meta', 'icon', 'iconVariant', 'avatar', 'fallback']
--}}
@props(['notifications' => []])

<button
    type="button"
    class="button button--ghost button--neutral button--icon-only"
    data-stisla-popover-trigger="topbarNotif"
    aria-haspopup="dialog"
    aria-expanded="false"
    aria-controls="topbarNotif"
    aria-label="Notifications"
>
    <x-ui.icon name="notification-3-line" />
</button>

<div
    class="popover popover--menu w-80 max-w-full shadow-xl"
    id="topbarNotif"
    data-stisla-popover
    data-stisla-popover-placement="bottom-end"
>
    <div class="popover__header">
        <h3 class="popover__title">Notifications</h3>
    </div>

    <div class="popover__body">
        @forelse($notifications as $notification)
            <x-ui.media-item
                :href="$notification['href'] ?? '#'"
                :title="$notification['title']"
                :description="$notification['description']"
                :meta="$notification['meta']"
                :icon="$notification['icon'] ?? null"
                :iconVariant="$notification['iconVariant'] ?? 'primary'"
                :avatarSrc="$notification['avatar'] ?? null"
                :avatarFallback="$notification['fallback'] ?? ''"
            />
        @empty
            <p class="text-sm text-muted-foreground p-4">Belum ada notifikasi.</p>
        @endforelse
    </div>

    <div class="popover__footer">
        <a href="#" class="button button--block button--outline button--neutral">
            View all notifications
            <x-ui.icon name="arrow-right-circle-line" />
        </a>
    </div>
</div>
