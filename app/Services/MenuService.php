<?php

namespace App\Services;

use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Support\TableResponseFormatter;
use Illuminate\Http\Request;

class MenuService
{
    public function __construct(protected MenuRepositoryInterface $menuRepository) {}

    public function list(int $perPage = 15)
    {
        return $this->menuRepository->paginate($perPage);
    }

    public function find(int|string $id)
    {
        return $this->menuRepository->find($id)->load('parent', 'icon');
    }

    public function create(array $data)
    {
        $menu = $this->menuRepository->create($data);
        return $menu->load('parent', 'icon');
    }

    public function update(int|string $id, array $data)
    {
        $menu = $this->menuRepository->update($id, $data);
        return $menu->load('parent', 'icon');
    }

    public function delete(int|string $id): bool
    {
        return $this->menuRepository->delete($id);
    }

    public function allForOptions()
    {
        return $this->menuRepository->allOrderedByOrder();
    }

    public function tree()
    {
        $menus = $this->menuRepository->query()
            ->with(['childrenRecursive', 'icon'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        return $this->formatTree($menus);
    }

    protected function formatTree($menus)
    {
        return $menus->map(function ($menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->name,
                'link' => $menu->link,
                'is_active' => $menu->is_active,
                'icon' => $menu->icon ? ['id' => $menu->icon->id, 'value' => $menu->icon->value] : null,
                'children' => $this->formatTree($menu->childrenRecursive),
            ];
        })->values()->all();
    }

    protected function baseQuery(Request $request)
    {
        $query = $this->menuRepository->query()->with('parent', 'icon');

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->get('parent_id'));
        }

        return $query;
    }

    public function table(Request $request): array
    {
        $query = $this->baseQuery($request);

        $paginated = $this->menuRepository->paginateFiltered(
            request: $request,
            searchableColumns: ['name', 'link'],
            sortableColumns: ['name', 'order', 'is_active', 'created_at'],
            defaultSort: 'order',
            query: $query,
        );

        return TableResponseFormatter::format($paginated, fn ($menu) => [
            'id' => $menu->id,
            'name' => $menu->name,
            'link' => $menu->link,
            'order' => $menu->order,
            'is_active' => $menu->is_active,
            'parent' => $menu->parent ? ['id' => $menu->parent->id, 'name' => $menu->parent->name] : null,
            'icon' => $menu->icon ? ['id' => $menu->icon->id, 'value' => $menu->icon->value] : null,
        ]);
    }
}
