<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

trait ProvidesTableData
{
    /**
     * Generate response JSON standar untuk tabel: search + sort + paginate.
     *
     * @param  Builder  $query               Query dasar (boleh sudah ada `with()`, `where()` tetap, dll)
     * @param  Request  $request             Request saat ini (baca query string page/search/sort/direction)
     * @param  array    $searchableColumns   Kolom yang boleh dicari lewat parameter `search`
     * @param  array    $sortableColumns     Kolom yang boleh dipakai untuk `sort` (whitelist, cegah SQL injection lewat nama kolom)
     * @param  string   $defaultSort         Kolom default kalau `sort` tidak dikirim / tidak valid
     * @param  callable|null $transform      Callback opsional untuk membentuk ulang tiap row jadi array response
     */
    protected function tableData(
        Builder $query,
        Request $request,
        array $searchableColumns = [],
        array $sortableColumns = [],
        string $defaultSort = 'created_at',
        ?callable $transform = null,
    ): JsonResponse {
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

        $perPage = min($request->integer('per_page', 10), 100); // cap supaya tidak diminta 999999 sekaligus

        $paginated = $query->orderBy($sort, $direction)->paginate($perPage)->withQueryString();

        $items = $transform
            ? collect($paginated->items())->map($transform)->values()
            : collect($paginated->items());

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
            ],
        ]);
    }
}
