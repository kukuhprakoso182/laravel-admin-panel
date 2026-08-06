{{-- Recent activity feed with a link to the full activity log. --}}
@props(['activities'])

@if ($activities->isNotEmpty())
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">Aktivitas Terbaru</h2>
            @if (Route::has('activity-logs.index'))
                <a href="{{ route('activity-logs.index') }}" class="text-xs text-blue-600 hover:underline">
                    Lihat semua
                </a>
            @endif
        </div>

        <ul class="divide-y divide-gray-100">
            @foreach ($activities as $activity)
                <x-molecules.activity-item :activity="$activity" />
            @endforeach
        </ul>
    </div>
@endif
