<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected RoleRepositoryInterface $roleRepository,
        protected PermissionRepositoryInterface $permissionRepository,
        protected MenuRepositoryInterface $menuRepository,
        protected ActivityLogRepositoryInterface $activityLogRepository,
    ) {
    }

    /**
     * Summary cards, filtered to only the ones the logged-in user's role
     * has "view" permission on (via role_menu_permissions, matched on
     * menus.link_alias == route name).
     */
    public function getSummaryCards(?User $user): array
    {
        $cards = [
            [
                'route' => 'users.index',
                'label' => 'Total User',
                'value' => $this->userRepository->query()->count(),
                'change' => $this->monthOverMonthChange($this->userRepository),
                'icon' => 'users',
                'color' => 'blue',
            ],
            [
                'route' => 'roles.index',
                'label' => 'Total Role',
                'value' => $this->roleRepository->query()->count(),
                'change' => $this->monthOverMonthChange($this->roleRepository),
                'icon' => 'shield',
                'color' => 'purple',
            ],
            [
                'route' => 'permissions.index',
                'label' => 'Total Permission',
                'value' => $this->permissionRepository->query()->count(),
                'change' => $this->monthOverMonthChange($this->permissionRepository),
                'icon' => 'key',
                'color' => 'amber',
            ],
            [
                'route' => 'menus.index',
                'label' => 'Total Menu',
                'value' => $this->menuRepository->query()->count(),
                'change' => $this->monthOverMonthChange($this->menuRepository),
                'icon' => 'menu',
                'color' => 'emerald',
            ],
        ];

        return array_values(array_filter(
            $cards,
            fn (array $card) => $this->hasMenuAccess($user, $card['route'])
        ));
    }

    /**
     * New records this month vs last month, as a percentage delta.
     */
    protected function monthOverMonthChange(BaseRepositoryInterface $repository): ?float
    {
        $table = $repository->query()->getModel()->getTable();

        if (! Schema::hasColumn($table, 'created_at')) {
            return null;
        }

        $startOfThisMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonthNoOverflow()->startOfMonth();

        $thisMonth = $repository->query()->where('created_at', '>=', $startOfThisMonth)->count();
        $lastMonth = $repository->query()->whereBetween('created_at', [$startOfLastMonth, $startOfThisMonth])->count();

        if ($lastMonth === 0) {
            return $thisMonth > 0 ? 100.0 : 0.0;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    /**
     * Daily new-user count for the last N days. Only computed if the
     * logged-in user's role can view the Users menu.
     */
    public function getUserGrowth(?User $user, int $days = 7): Collection
    {
        if (! $this->hasMenuAccess($user, 'users.index')) {
            return collect();
        }

        $table = $this->userRepository->query()->getModel()->getTable();

        if (! Schema::hasColumn($table, 'created_at')) {
            return collect();
        }

        $start = Carbon::now()->subDays($days - 1)->startOfDay();

        $raw = $this->userRepository->query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        return collect(range(0, $days - 1))->map(function (int $offset) use ($start, $raw) {
            $date = $start->copy()->addDays($offset);
            $key = $date->toDateString();

            return [
                'label' => $date->translatedFormat('D'),
                'date' => $key,
                'total' => (int) ($raw[$key] ?? 0),
            ];
        });
    }

    /**
     * Distribution of users per role. Only computed if the logged-in
     * user's role can view the Roles menu.
     */
    public function getRoleBreakdown(?User $user, int $limit = 6): Collection
    {
        if (! $this->hasMenuAccess($user, 'roles.index')) {
            return collect();
        }

        $roleTable = $this->roleRepository->query()->getModel()->getTable();

        $rows = $this->roleRepository->query()
            ->select("{$roleTable}.id", "{$roleTable}.name", DB::raw('COUNT(user_roles.user_id) as total'))
            ->join('user_roles', 'user_roles.role_id', '=', "{$roleTable}.id")
            ->groupBy("{$roleTable}.id", "{$roleTable}.name")
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        $totalUsers = max($rows->sum('total'), 1);

        return $rows->map(fn ($row) => [
            'role' => $row->name,
            'total' => $row->total,
            'percentage' => round(($row->total / $totalUsers) * 100),
        ]);
    }

    /**
     * Latest activity log entries. Only computed if the logged-in user's
     * role can view the Log Aktivitas menu.
     */
    public function getRecentActivities(?User $user, int $limit = 8): Collection
    {
        if (! $this->hasMenuAccess($user, 'activity-logs.index')) {
            return collect();
        }

        return $this->activityLogRepository->query()
            ->with('causer')
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function (ActivityLog $log) {
                return [
                    'description' => $log->description ?? $this->humanizeActivity($log),
                    'causer' => $log->causer?->name ?? 'Sistem',
                    'event' => $log->event,
                    'color' => $this->eventColor($log->event),
                    'created_at' => $log->created_at,
                ];
            });
    }

    /**
     * Quick-action links, filtered to only menus the logged-in user's
     * role has "view" permission on.
     */
    public function getQuickLinks(?User $user): array
    {
        $links = [
            ['label' => 'Kelola User', 'route' => 'users.index'],
            ['label' => 'Kelola Role', 'route' => 'roles.index'],
            ['label' => 'Kelola Permission', 'route' => 'permissions.index'],
            ['label' => 'Kelola Menu', 'route' => 'menus.index'],
            ['label' => 'Kelola Icon', 'route' => 'icons.index'],
            ['label' => 'Log Aktivitas', 'route' => 'activity-logs.index'],
        ];

        return array_values(array_filter(
            $links,
            fn (array $link) => \Illuminate\Support\Facades\Route::has($link['route'])
                && $this->hasMenuAccess($user, $link['route'])
        ));
    }

    /**
     * Whether the given user's role(s) have "view" permission on the menu
     * whose `link_alias` matches the given route name.
     *
     * Group/parent menus (Settings, User Management, Menu Management) only
     * ever get 'view' in the seeder — leaf menus (Users, Roles, etc.) get
     * full CRUD. Dashboard cards only ever check leaf menus by route name,
     * so this always resolves to a real, permission-checked menu row.
     */
    protected function hasMenuAccess(?User $user, string $routeName): bool
    {
        if (! $user) {
            return false;
        }

        $menu = $this->menuRepository->query()
            ->where('link_alias', $routeName)
            ->first();

        if (! $menu) {
            // Menu not registered in the menu table — don't hide by default.
            // (Dashboard cards fail OPEN; action buttons elsewhere fail CLOSED —
            // see table-row-actions / data-table, which is a different, stricter check.)
            return true;
        }

        return $user->hasPermission('view', $menu->id);
    }

    protected function humanizeActivity(ActivityLog $log): string
    {
        $eventLabels = [
            'login' => 'Login ke sistem',
            'logout' => 'Logout dari sistem',
            'login_failed' => 'Percobaan login gagal',
            'created' => 'Membuat data',
            'updated' => 'Memperbarui data',
            'deleted' => 'Menghapus data',
        ];

        $label = $eventLabels[$log->event] ?? ucfirst(str_replace('_', ' ', $log->event));

        if ($log->subject_type) {
            $subjectName = class_basename($log->subject_type);
            return "{$label} {$subjectName}";
        }

        return $label;
    }

    public function eventColor(?string $event): string
    {
        return match ($event) {
            'created', 'login' => 'bg-emerald-500',
            'updated' => 'bg-blue-500',
            'deleted', 'login_failed' => 'bg-red-500',
            'logout' => 'bg-gray-400',
            default => 'bg-gray-300',
        };
    }
}
