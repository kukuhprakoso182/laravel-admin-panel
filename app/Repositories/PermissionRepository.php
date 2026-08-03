<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use DateTimeInterface;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public function allForOptions()
    {
        return $this->model->orderBy('name')->get();
    }

    public function countTotal(): int
    {
        return Permission::count('id');
    }

    public function countCreatedBetween(DateTimeInterface $start, DateTimeInterface $end): int
    {
        return Permission::whereBetween('created_at', [$start, $end], 'and', false)->count();
    }
}
