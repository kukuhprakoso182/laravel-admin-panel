{{-- One day's column in the 7-day user growth bar chart. --}}
@props(['day', 'max' => 1])

<div class="flex-1 flex flex-col items-center gap-2">
    <span class="text-xs text-gray-500">{{ $day['total'] }}</span>
    <div class="w-full bg-gray-100 rounded-md flex items-end" style="height: 100px;">
        <div class="w-full bg-blue-500 rounded-md transition-all"
             style="height: {{ max(6, round(($day['total'] / $max) * 100)) }}%"></div>
    </div>
    <span class="text-xs text-gray-400">{{ $day['label'] }}</span>
</div>
