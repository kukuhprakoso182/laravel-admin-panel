<?php

namespace App\Services;

use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Services\Concerns\HandlesForeignKeyViolation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MenuService extends BaseService
{
    use HandlesForeignKeyViolation;

    public function __construct(protected MenuRepositoryInterface $menuRepository)
    {
        parent::__construct($menuRepository);
    }

    protected function repository(): object
    {
        return $this->menuRepository;
    }

    // Override: find butuh eager-load relasi tambahan
    public function find(int|string $id)
    {
        return $this->menuRepository->find($id)->load('parent', 'icon');
    }

    // Override: create butuh load relasi setelah insert
    public function create(array $data)
    {
        $menu = $this->menuRepository->create($data);

        return $menu->load('parent', 'icon');
    }

    // Override: update butuh load relasi setelah update
    public function update(int|string $id, array $data)
    {
        $menu = $this->menuRepository->update($id, $data);

        return $menu->load('parent', 'icon');
    }

    // Override: delete butuh handle foreign key violation (dipakai sebagai parent_id di menu lain)
    public function delete(int|string $id): bool
    {
        return $this->deleteOrFailOnForeignKey(
            fn () => $this->menuRepository->delete($id),
            'menu_id'
        );
    }

    // Method khusus Menu, tidak ada di base
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
        return $menus->map(fn ($menu) => [
            'id' => $menu->id,
            'name' => $menu->name,
            'link' => $menu->link,
            'is_active' => $menu->is_active,
            'icon' => $menu->icon ? ['id' => $menu->icon->id, 'value' => $menu->icon->value] : null,
            'children' => $this->formatTree($menu->childrenRecursive),
        ])->values()->all();
    }

    // Override: query dasar butuh eager-load + filter parent_id
    protected function baseQuery(Request $request): Builder
    {
        $query = parent::baseQuery($request)->with('parent', 'icon');

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        return $query;
    }

    // Override: default sort beda dari base ('created_at')
    protected function defaultSort(): string
    {
        return 'order';
    }

    protected function searchableColumns(): array
    {
        return ['name', 'link'];
    }

    protected function sortableColumns(): array
    {
        return ['name', 'order', 'is_active', 'created_at'];
    }

    protected function formatRow(mixed $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'link' => $item->link,
            'order' => $item->order,
            'is_active' => $item->is_active,
            'parent' => $item->parent ? ['id' => $item->parent->id, 'name' => $item->parent->name] : null,
            'icon' => $item->icon ? ['id' => $item->icon->id, 'value' => $item->icon->value] : null,
        ];
    }
}
