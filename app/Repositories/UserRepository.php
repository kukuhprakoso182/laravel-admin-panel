<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function syncRoles(int|string $userId, array $roleIds)
    {
        $user = $this->find($userId);
        $user->roles()->sync($roleIds);

        app(\App\Services\SidebarService::class)->clearForUser($userId);

        return $user->load('roles');
    }

    public function syncMenuPermissions(int|string $roleId, array $menuPermissionPairs)
    {
        $role = $this->find($roleId);
        $role->roleMenuPermissions()->delete();

        foreach ($menuPermissionPairs as $pair) {
            $role->roleMenuPermissions()->create([
                'menu_id' => $pair['menu_id'],
                'permission_id' => $pair['permission_id'],
            ]);
        }

        app(\App\Services\SidebarService::class)->clearAll();

        return $role->load('roleMenuPermissions.menu', 'roleMenuPermissions.permission');
    }

    public function paginateFiltered(
        Request $request,
        array $searchableColumns = [],
        array $sortableColumns = [],
        string $defaultSort = 'created_at',
        ?Builder $query = null,
    ): LengthAwarePaginator {
        // User butuh eager load 'roles' supaya tidak N+1 saat di-transform di Service
        return parent::paginateFiltered(
            $request,
            $searchableColumns,
            $sortableColumns,
            $defaultSort,
            $query ?? $this->model->newQuery()->with('roles'),
        );
    }

    public function countTotal(): int
    {
        return User::count('id');
    }

    public function countCreatedBetween(DateTimeInterface $start, DateTimeInterface $end): int
    {
        return User::whereBetween('created_at', [$start, $end], 'and', false)->count();
    }

    public function dailyCountsSince(DateTimeInterface $start): Collection
    {
        return User::where('created_at', '>=', $start, 'and')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');
    }

    public function roleDistribution(int $limit = 6): Collection
    {
        return DB::table('user_roles')
            ->select('role_id', DB::raw('COUNT(*) as total'))
            ->groupBy('role_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => ['role_id' => $row->role_id, 'total' => $row->total]);
    }
}
