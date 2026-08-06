{{-- 7-day new-user bar chart. --}}
@props(['growth'])

@if ($growth->isNotEmpty())
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">User Baru (7 Hari Terakhir)</h2>
            <span class="text-xs text-gray-400">
                Total: {{ $growth->sum('total') }}
            </span>
        </div>

        @if ($growth->sum('total') > 0)
            @php $max = max(1, $growth->max('total')); @endphp
            <div class="flex items-end justify-between gap-2 h-40">
                @foreach ($growth as $day)
                    <x-molecules.growth-bar :day="$day" :max="$max" />
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 py-10 text-center">
                Belum ada user baru dalam 7 hari terakhir.
            </p>
        @endif
    </div>
@endif
