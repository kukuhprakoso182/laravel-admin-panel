{{-- One clickable KPI card: icon, label, value, and optional change badge. --}}
@props(['card'])

<a href="{{ Route::has($card['route']) ? route($card['route']) : '#' }}"
   class="block bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
            <p class="mt-1 text-2xl font-semibold text-gray-800">
                {{ number_format($card['value']) }}
            </p>
        </div>
        <x-atoms.icon-badge :color="$card['color']" :icon="$card['icon']" />
    </div>

    <x-atoms.change-badge :value="$card['change']" />
</a>
