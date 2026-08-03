<x-layouts.app>
    <x-slot:title>Dashboard</x-slot:title>

    <div class="space-y-6">

        {{-- Greeting --}}
        @php
            $hour = now()->hour;
            $greeting = match (true) {
                $hour >= 4 && $hour < 11 => 'Selamat Pagi',
                $hour >= 11 && $hour < 15 => 'Selamat Siang',
                $hour >= 15 && $hour < 18 => 'Selamat Sore',
                default => 'Selamat Malam',
            };
        @endphp

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-800">
                    {{ $greeting }}, {{ $user->name ?? 'Pengguna' }} 👋
                </h1>
                <p class="text-sm text-gray-500">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>

        {{-- Summary Cards --}}
        @if (count($summaryCards) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($summaryCards as $card)
                    @php
                        $colorMap = [
                            'blue' => 'bg-blue-50 text-blue-600',
                            'purple' => 'bg-purple-50 text-purple-600',
                            'amber' => 'bg-amber-50 text-amber-600',
                            'emerald' => 'bg-emerald-50 text-emerald-600',
                        ];
                        $iconBg = $colorMap[$card['color']] ?? 'bg-gray-50 text-gray-600';
                    @endphp

                    <a href="{{ Route::has($card['route']) ? route($card['route']) : '#' }}"
                       class="block bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
                                <p class="mt-1 text-2xl font-semibold text-gray-800">
                                    {{ number_format($card['value']) }}
                                </p>
                            </div>
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg {{ $iconBg }}">
                                @include('pages.dashboard.partials.icon', ['name' => $card['icon']])
                            </span>
                        </div>

                        @if (! is_null($card['change']))
                            <p class="mt-3 text-xs font-medium {{ $card['change'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $card['change'] >= 0 ? '▲' : '▼' }} {{ abs($card['change']) }}%
                                <span class="text-gray-400 font-normal">vs bulan lalu</span>
                            </p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif

        @if ($userGrowth->isNotEmpty() || $roleBreakdown->isNotEmpty())
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- User growth chart (7 days) --}}
                @if ($userGrowth->isNotEmpty())
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base font-semibold text-gray-800">User Baru (7 Hari Terakhir)</h2>
                            <span class="text-xs text-gray-400">
                                Total: {{ $userGrowth->sum('total') }}
                            </span>
                        </div>

                        @if ($userGrowth->sum('total') > 0)
                            @php $max = max(1, $userGrowth->max('total')); @endphp
                            <div class="flex items-end justify-between gap-2 h-40">
                                @foreach ($userGrowth as $day)
                                    <div class="flex-1 flex flex-col items-center gap-2">
                                        <span class="text-xs text-gray-500">{{ $day['total'] }}</span>
                                        <div class="w-full bg-gray-100 rounded-md flex items-end" style="height: 100px;">
                                            <div class="w-full bg-blue-500 rounded-md transition-all"
                                                 style="height: {{ max(6, round(($day['total'] / $max) * 100)) }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $day['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 py-10 text-center">
                                Belum ada user baru dalam 7 hari terakhir.
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Role distribution --}}
                @if ($roleBreakdown->isNotEmpty())
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h2 class="text-base font-semibold text-gray-800 mb-4">Distribusi User per Role</h2>

                        <div class="space-y-3">
                            @foreach ($roleBreakdown as $item)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-700">{{ $item['role'] }}</span>
                                        <span class="text-gray-400">{{ $item['total'] }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $item['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if ($recentActivities->isNotEmpty() || count($quickLinks) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- Recent activity --}}
                @if ($recentActivities->isNotEmpty())
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
                            @foreach ($recentActivities as $activity)
                                <li class="py-3 flex items-start gap-3">
                                    <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $activity['color'] }}"></span>

                                    <div class="flex-1">
                                        <p class="text-sm text-gray-700">{{ $activity['description'] }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">oleh {{ $activity['causer'] }}</p>
                                    </div>

                                    <span class="text-xs text-gray-400 whitespace-nowrap">
                                        {{ optional($activity['created_at'])->diffForHumans() }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Quick actions --}}
                @if (count($quickLinks) > 0)
                    <div class="{{ $recentActivities->isNotEmpty() ? '' : 'lg:col-span-3' }} bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h2 class="text-base font-semibold text-gray-800 mb-4">Akses Cepat</h2>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($quickLinks as $link)
                                <a href="{{ route($link['route']) }}"
                                   class="text-sm text-center px-3 py-3 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 transition">
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Fallback if the role has no visible dashboard content at all --}}
        @if (count($summaryCards) === 0 && $userGrowth->isEmpty() && $roleBreakdown->isEmpty() && $recentActivities->isEmpty() && count($quickLinks) === 0)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-10 text-center">
                <p class="text-sm text-gray-400">
                    Belum ada modul yang bisa ditampilkan untuk role Anda saat ini.
                </p>
            </div>
        @endif
    </div>
</x-layouts.app>
