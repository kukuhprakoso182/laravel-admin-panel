{{-- One row in the recent-activity feed. --}}
@props(['activity'])

<li class="py-3 flex items-start gap-3">
    <x-atoms.status-dot :color="$activity['color']" />

    <div class="flex-1">
        <p class="text-sm text-gray-700">{{ $activity['description'] }}</p>
        <p class="text-xs text-gray-400 mt-0.5">oleh {{ $activity['causer'] }}</p>
    </div>

    <span class="text-xs text-gray-400 whitespace-nowrap">
        {{ optional($activity['created_at'])->diffForHumans() }}
    </span>
</li>
