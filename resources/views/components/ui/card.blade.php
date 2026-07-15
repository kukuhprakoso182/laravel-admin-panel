{{--
    Pakai:
    <x-ui.card title="Recent Orders">
        <x-slot:action>
            <a href="{{ route('orders.index') }}" class="ms-auto button button--neutral button--ghost button--sm">View all</a>
        </x-slot:action>

        ...isi card...
    </x-ui.card>
--}}
@props(['title' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || isset($action))
        <div class="card__header">
            @if($title)
                <span class="card__title">{{ $title }}</span>
            @endif
            {{ $action ?? '' }}
        </div>
    @endif

    <div class="card__body">
        {{ $slot }}
    </div>
</div>
