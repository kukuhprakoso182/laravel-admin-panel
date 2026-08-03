<?php

namespace App\Services;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Http\Request;

class PermissionService
{
    public function __construct(protected PermissionRepositoryInterface $permissionRepository) {}

    public function data(Request $request)
    {
        return $this->permissionRepository->paginateFiltered(
            $request,
            searchableColumns: ['name', 'description'],
            sortableColumns: ['name', 'created_at'],
            defaultSort: 'name',
        );
    }

    public function list(int $perPage = 15)
    {
        return $this->permissionRepository->paginate($perPage);
    }

    public function allForOptions()
    {
        return $this->permissionRepository->allForOptions();
    }

    public function find(int|string $id)
    {
        return $this->permissionRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->permissionRepository->create($data);
    }

    public function update(int|string $id, array $data)
    {
        return $this->permissionRepository->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        return $this->permissionRepository->delete($id);
    }
}
