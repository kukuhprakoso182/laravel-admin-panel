<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*']);
    public function paginate(int $perPage = 15);
    public function find(int|string $id);
    public function create(array $data);
    public function update(int|string $id, array $data);
    public function delete(int|string $id): bool;
    public function paginateFiltered(
        Request $request,
        array $searchableColumns = [],
        array $sortableColumns = [],
        string $defaultSort = 'created_at',
        ?Builder $query = null,
    ): LengthAwarePaginator;
    public function query(): Builder;
}
