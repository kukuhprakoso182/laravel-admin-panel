<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use DateTimeInterface;
use Illuminate\Support\Collection;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function syncMenuPermissions(int|string $roleId, array $menuPermissionPairs)
    {
        $role = $this->find($roleId);

        $role->menuPermissions()->delete();

        foreach ($menuPermissionPairs as $pair) {
            $role->menuPermissions()->create([
                'menu_id' => $pair['menu_id'],
                'permission_id' => $pair['permission_id'],
            ]);
        }

        return $role->load('menuPermissions.menu', 'menuPermissions.permission');
    }

    public function getAssignedMenuPermissionPairs(int|string $roleId): array
    {
        $role = $this->find($roleId);

        return $role->menuPermissions
            ->map(fn ($pivot) => "{$pivot->menu_id}:{$pivot->permission_id}")
            ->all();
    }

    public function allOrderedByName()
    {
        return $this->model->orderBy('name')->get();
    }

    public function countTotal(): int
    {
        return Role::count('id');
    }

    public function countCreatedBetween(DateTimeInterface $start, DateTimeInterface $end): int
    {
        return Role::whereBetween('created_at', [$start, $end], 'and', false)->count();
    }

    public function namesByIds(array $ids): Collection
    {
        return Role::whereIn('id', $ids, 'and', false)->pluck('name', 'id');
    }
}
