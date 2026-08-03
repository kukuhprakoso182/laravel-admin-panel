<?php

namespace App\Repositories\Contracts;

use DateTimeInterface;
use Illuminate\Support\Collection;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function syncMenuPermissions(int|string $roleId, array $menuPermissionPairs);
    public function allOrderedByName();
    public function getAssignedMenuPermissionPairs(int|string $roleId): array;
    public function countTotal(): int;

    public function countCreatedBetween(DateTimeInterface $start, DateTimeInterface $end): int;

    public function namesByIds(array $ids): Collection;
}
