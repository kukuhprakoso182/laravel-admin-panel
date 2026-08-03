<?php

namespace App\Repositories;

use App\Models\Icon;
use App\Repositories\Contracts\IconRepositoryInterface;

class IconRepository extends BaseRepository implements IconRepositoryInterface
{
    public function __construct(Icon $model)
    {
        parent::__construct($model);
    }

    public function allOrderedByValue()
    {
        return $this->model->orderBy('section')->orderBy('value')->get();
    }
}
