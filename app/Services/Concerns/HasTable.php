<?php

namespace App\Services\Concerns;

use App\Support\TableResponseFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasTable
{
    abstract protected function repository(): object;
    abstract protected function searchableColumns(): array;
    abstract protected function sortableColumns(): array;
    abstract protected function formatRow(mixed $item): array;

    protected function defaultSort(): string
    {
        return 'created_at';
    }

    protected function baseQuery(Request $request): Builder
    {
        return $this->repository()->query();
    }

    public function table(Request $request): array
    {
        $paginated = $this->repository()->paginateFiltered(
            request: $request,
            searchableColumns: $this->searchableColumns(),
            sortableColumns: $this->sortableColumns(),
            defaultSort: $this->defaultSort(),
            query: $this->baseQuery($request),
        );

        return TableResponseFormatter::format($paginated, fn ($item) => $this->formatRow($item));
    }
}
