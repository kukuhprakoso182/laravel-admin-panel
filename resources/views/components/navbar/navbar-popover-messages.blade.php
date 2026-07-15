{{--
    $messages = collection/array of:
    ['href' => ..., 'title' => ..., 'description' => ..., 'meta' => ..., 'avatar' => ..., 'fallback' => ..., 'unread' => bool]
    Idealnya di-passing dari controller/composer, bukan hardcode di view.
--}}
@props(['messages' => []])

<div class="relative">
    <button
        type="button"
        class="button button--ghost button--neutral button--icon-only"
        data-stisla-popover-trigger="messageNotif"
        aria-haspopup="dialog"
        aria-expanded="false"
        aria-controls="messageNotif"
        aria-label="Messages"
    >
        <x-ui.icon name="chat-3-line" />
    </button>
    @if(collect($messages)->contains(fn ($m) => $m['unread'] ?? false))
        <span
            class="indicator indicator--warning indicator--pulse absolute top-0 inset-e-0 translate-x-1/2 -translate-y-1/2"
        ></span>
    @endif
</div>

<div
    class="popover popover--menu w-88 max-w-full shadow-xl"
    id="messageNotif"
    data-stisla-popover
    data-stisla-popover-placement="bottom-end"
>
    <div class="popover__header">
        <h3 class="popover__title">Message</h3>
        <div class="popover__action">
            <a class="button button--sm button--ghost button--primary button--flush-end">Mark all as read</a>
        </div>
    </div>

    <div class="popover__body">
        @forelse($messages as $message)
            <x-ui.media-item
                :href="$message['href'] ?? '#'"
                :title="$message['title']"
                :description="$message['description']"
                :meta="$message['meta']"
                :avatarSrc="$message['avatar'] ?? null"
                :avatarFallback="$message['fallback'] ?? ''"
                :unread="$message['unread'] ?? false"
            />
        @empty
            <p class="text-sm text-muted-foreground p-4">Belum ada pesan.</p>
        @endforelse
    </div>

    <div class="popover__footer">
        <a href="#" class="button button--block button--outline button--neutral">
            View all messages
            <x-ui.icon name="arrow-right-circle-line" />
        </a>
    </div>
</div>
