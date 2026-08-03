<?php

namespace App\Repositories\Contracts;

use DateTimeInterface;
use Illuminate\Support\Collection;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function syncRoles(int|string $userId, array $roleIds);

    public function syncMenuPermissions(int|string $roleId, array $menuPermissionPairs);
    public function countTotal(): int;

    public function countCreatedBetween(DateTimeInterface $start, DateTimeInterface $end): int;

    public function dailyCountsSince(DateTimeInterface $start): Collection;

    public function roleDistribution(int $limit = 6): Collection;
}
