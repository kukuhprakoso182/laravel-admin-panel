<x-layouts.app title="Dashboard">

    <div class="space-y-6">

        <x-organisms.dashboard-greeting :name="$user->name ?? 'Pengguna'" />

        <x-organisms.summary-cards :cards="$summaryCards" />

        @if ($userGrowth->isNotEmpty() || $roleBreakdown->isNotEmpty())
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <x-organisms.user-growth-chart :growth="$userGrowth" />
                <x-organisms.role-breakdown :roles="$roleBreakdown" />
            </div>
        @endif

        @if ($recentActivities->isNotEmpty() || count($quickLinks) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <x-organisms.recent-activities :activities="$recentActivities" />
                <x-organisms.quick-links :links="$quickLinks" :full-width="$recentActivities->isEmpty()" />
            </div>
        @endif

        @if (count($summaryCards) === 0 && $userGrowth->isEmpty() && $roleBreakdown->isEmpty() && $recentActivities->isEmpty() && count($quickLinks) === 0)
            <x-organisms.empty-state />
        @endif
    </div>
</x-layouts.app>
