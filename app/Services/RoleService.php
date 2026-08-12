<?php

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Services\Concerns\HandlesForeignKeyViolation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RoleService extends BaseService
{
    use HandlesForeignKeyViolation;

    public function __construct(protected RoleRepositoryInterface $roleRepository)
    {
        parent::__construct($roleRepository);
    }

    protected function repository(): object
    {
        return $this->roleRepository;
    }

    // Override: delete butuh handle foreign key violation (role dipakai di user_has_roles)
    public function delete(int|string $id): bool
    {
        return $this->deleteOrFailOnForeignKey(
            fn () => $this->roleRepository->delete($id),
            'role_id'
        );
    }

    public function assignMenuPermissions(int|string $roleId, array $menuPermissionPairs)
    {
        return $this->roleRepository->syncMenuPermissions($roleId, $menuPermissionPairs);
    }

    public function allForOptions()
    {
        return $this->roleRepository->allOrderedByName();
    }

    public function menuPermissionMatrix(int|string $roleId): array
    {
        $role = $this->roleRepository->find($roleId);

        return [
            'role' => ['id' => $role->id, 'name' => $role->name],
            'assigned' => $this->roleRepository->getAssignedMenuPermissionPairs($roleId),
        ];
    }

    protected function baseQuery(Request $request): Builder
    {
        return parent::baseQuery($request)->withCount('users');
    }

    protected function searchableColumns(): array
    {
        return ['name', 'description'];
    }

    protected function sortableColumns(): array
    {
        return ['name', 'created_at'];
    }

    protected function formatRow(mixed $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'users_count' => $item->users_count,
            'created_at' => $item->created_at,
        ];
    }
}
