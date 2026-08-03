<?php

namespace App\Services;

use App\Repositories\Contracts\IconRepositoryInterface;
use App\Support\TableResponseFormatter;
use Illuminate\Http\Request;

class IconService
{
    public function __construct(protected IconRepositoryInterface $iconRepository)
    {
    }

    public function list(int $perPage = 15)
    {
        return $this->iconRepository->paginate($perPage);
    }

    public function find(int|string $id)
    {
        return $this->iconRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->iconRepository->create($data);
    }

    public function update(int|string $id, array $data)
    {
        return $this->iconRepository->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        return $this->iconRepository->delete($id);
    }

    public function allForOptions()
    {
        return $this->iconRepository->allOrderedByValue();
    }

    protected function baseQuery(Request $request)
    {
        $query = $this->iconRepository->query();

        if ($request->filled('section')) {
            $query->where('section', $request->get('section'));
        }

        return $query;
    }

    public function table(Request $request): array
    {
        $query = $this->baseQuery($request);

        $paginated = $this->iconRepository->paginateFiltered(
            request: $request,
            searchableColumns: ['value', 'section'],
            sortableColumns: ['value', 'section', 'is_active', 'created_at'],
            query: $query,
        );

        return TableResponseFormatter::format($paginated, fn ($icon) => [
            'id' => $icon->id,
            'value' => $icon->value,
            'section' => $icon->section,
            'is_active' => $icon->is_active,
        ]);
    }
}
