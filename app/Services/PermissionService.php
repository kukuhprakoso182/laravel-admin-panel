<?php

namespace App\Services;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Services\Concerns\HandlesForeignKeyViolation;
use App\Services\Concerns\HasCrud;
use Illuminate\Http\Request;

class PermissionService
{
    use HasCrud, HandlesForeignKeyViolation;

    public function __construct(protected PermissionRepositoryInterface $permissionRepository)
    {
    }

    protected function repository(): object
    {
        return $this->permissionRepository;
    }

    // Method khusus Permission, tidak ada di base
    public function data(Request $request)
    {
        return $this->permissionRepository->paginateFiltered(
            $request,
            searchableColumns: ['name', 'description'],
            sortableColumns: ['name', 'created_at'],
            defaultSort: 'name',
        );
    }

    public function allForOptions()
    {
        return $this->permissionRepository->allForOptions();
    }

    public function delete(int|string $id): bool
    {
        return $this->deleteOrFailOnForeignKey(
            fn () => $this->permissionRepository->delete($id),
            'permission_id'
        );
    }
}
