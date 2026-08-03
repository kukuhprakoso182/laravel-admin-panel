<?php

namespace App\Repositories;

use App\Models\Menu;
use App\Repositories\Contracts\MenuRepositoryInterface;
use DateTimeInterface;

class MenuRepository extends BaseRepository implements MenuRepositoryInterface
{
    public function __construct(Menu $model)
    {
        parent::__construct($model);
    }

    public function tree()
    {
        return $this->model
            ->whereNull('parent_id')
            ->with('children.icon', 'icon')
            ->orderBy('order')
            ->get();
    }

    public function allOrderedByOrder()
    {
        return $this->model->orderBy('order')->get();
    }

    public function countTotal(): int
    {
        return Menu::count('id');
    }

    public function countCreatedBetween(DateTimeInterface $start, DateTimeInterface $end): int
    {
        return Menu::whereBetween('created_at', [$start, $end], 'and', false)->count();
    }
}
