<?php

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Services\Concerns\HasCrud;
use App\Services\Concerns\HasTable;

abstract class BaseService
{
    use HasCrud, HasTable;

    public function __construct(protected BaseRepositoryInterface $repository)
    {
    }

    protected function repository(): object
    {
        return $this->repository;
    }
}
