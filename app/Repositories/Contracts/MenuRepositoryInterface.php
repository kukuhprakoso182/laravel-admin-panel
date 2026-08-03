<?php

namespace App\Repositories\Contracts;

use DateTimeInterface;

interface MenuRepositoryInterface extends BaseRepositoryInterface {
    public function tree();
    public function allOrderedByOrder();
    public function countTotal(): int;
    public function countCreatedBetween(DateTimeInterface $start, DateTimeInterface $end): int;
}
