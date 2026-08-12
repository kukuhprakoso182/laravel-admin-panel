<?php

namespace App\Services;

use App\Repositories\Contracts\IconRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class IconService extends BaseService
{
    public function __construct(protected IconRepositoryInterface $iconRepository)
    {
        parent::__construct($iconRepository);
    }

    protected function repository(): object
    {
        return $this->iconRepository;
    }

    // Method khusus Icon, tidak ada di base
    public function allForOptions()
    {
        return $this->iconRepository->allOrderedByValue();
    }

    // Override: tambah filter section di atas query dasar
    protected function baseQuery(Request $request): Builder
    {
        $query = parent::baseQuery($request);

        if ($request->filled('section')) {
            $query->where('section', $request->get('section'));
        }

        return $query;
    }

    protected function searchableColumns(): array
    {
        return ['value', 'section'];
    }

    protected function sortableColumns(): array
    {
        return ['value', 'section', 'is_active', 'created_at'];
    }

    protected function formatRow(mixed $item): array
    {
        return [
            'id' => $item->id,
            'value' => $item->value,
            'section' => $item->section,
            'is_active' => $item->is_active,
        ];
    }
}
