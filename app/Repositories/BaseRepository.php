<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*'])
    {
        return $this->model->get($columns);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function find(int|string $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int|string $id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->find($id)->delete();
    }

    // ... method all()/find()/create()/update()/delete() yang sudah ada tetap sama ...

    /**
     * Search + sort (whitelist) + paginate generik di level query,
     * TIDAK tahu apa-apa soal HTTP/JSON — murni data layer.
     *
     * @param  Builder|null  $query  Query dasar custom (misal sudah ada with()).
     *                               Kalau null, pakai $this->model->newQuery() polos.
     */
    public function paginateFiltered(
        Request $request,
        array $searchableColumns = [],
        array $sortableColumns = [],
        string $defaultSort = 'created_at',
        ?Builder $query = null,
    ): LengthAwarePaginator {
        $query ??= $this->model->newQuery();

        if ($request->filled('search') && !empty($searchableColumns)) {
            $search = $request->string('search');

            $query->where(function ($q) use ($searchableColumns, $search) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        $sort = $request->get('sort');
        $sort = in_array($sort, $sortableColumns, true) ? $sort : $defaultSort;
        $direction = $request->get('direction') === 'asc' ? 'asc' : 'desc';

        $perPage = min($request->integer('per_page', 10), 100); // cap, cegah diminta ribuan sekaligus

        return $query->orderBy($sort, $direction)->paginate($perPage)->withQueryString();
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }
}
