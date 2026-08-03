<?php

namespace App\Repositories\Contracts;

interface IconRepositoryInterface extends BaseRepositoryInterface
{
    public function allOrderedByValue();
}
