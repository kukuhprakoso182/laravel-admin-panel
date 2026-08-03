<?php

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Support\TableResponseFormatter;
use Illuminate\Http\Request;

class RoleService
{
    public function __construct(protected RoleRepositoryInterface $roleRepository)
    {
    }

    public function list(int $perPage = 15)
    {
        return $this->roleRepository->paginate($perPage);
    }

    public function find(int|string $id)
    {
        return $this->roleRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->roleRepository->create($data);
    }

    public function update(int|string $id, array $data)
    {
        return $this->roleRepository->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        return $this->roleRepository->delete($id);
    }

    public function assignMenuPermissions(int|string $roleId, array $menuPermissionPairs)
    {
        return $this->roleRepository->syncMenuPermissions($roleId, $menuPermissionPairs);
    }

    public function allForOptions()
    {
        return $this->roleRepository->allOrderedByName();
    }

    protected function baseQuery(Request $request)
    {
        return $this->roleRepository->query()->withCount('users');
    }

    public function table(Request $request): array
    {
        $query = $this->baseQuery($request);

        $paginated = $this->roleRepository->paginateFiltered(
            request: $request,
            searchableColumns: ['name', 'description'],
            sortableColumns: ['name', 'created_at'],
            query: $query,
        );

        return TableResponseFormatter::format($paginated, fn ($role) => [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'users_count' => $role->users_count,
            'created_at' => $role->created_at,
        ]);
    }

    public function menuPermissionMatrix(int|string $roleId): array
    {
        $role = $this->roleRepository->find($roleId);

        return [
            'role' => ['id' => $role->id, 'name' => $role->name],
            'assigned' => $this->roleRepository->getAssignedMenuPermissionPairs($roleId),
        ];
    }
}
