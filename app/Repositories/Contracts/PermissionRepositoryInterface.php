<?php

namespace App\Repositories\Contracts;

use DateTimeInterface;

interface PermissionRepositoryInterface extends BaseRepositoryInterface {
    public function allForOptions();
    public function countTotal(): int;
    public function countCreatedBetween(DateTimeInterface $start, DateTimeInterface $end): int;
}
