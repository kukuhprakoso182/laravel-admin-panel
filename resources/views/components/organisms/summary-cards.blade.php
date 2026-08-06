{{-- Grid of KPI summary cards. --}}
@props(['cards' => []])

@if (count($cards) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($cards as $card)
            <x-molecules.summary-card :card="$card" />
        @endforeach
    </div>
@endif
