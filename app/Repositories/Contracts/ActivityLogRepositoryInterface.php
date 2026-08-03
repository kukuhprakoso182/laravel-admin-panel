<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ActivityLogRepositoryInterface extends BaseRepositoryInterface
{
    public function latestWithCauser(int $limit = 8): Collection;
}
