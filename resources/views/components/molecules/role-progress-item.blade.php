{{-- One row in the role-distribution breakdown: label, count, progress bar. --}}
@props(['item'])

<div>
    <div class="flex justify-between text-sm mb-1">
        <span class="text-gray-700">{{ $item['role'] }}</span>
        <span class="text-gray-400">{{ $item['total'] }}</span>
    </div>
    <x-atoms.progress-bar :percentage="$item['percentage']" color="bg-purple-500" />
</div>
