<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;

class TableResponseFormatter
{
    public static function format(LengthAwarePaginator $paginator, ?callable $transform = null): array
    {
        $items = collect($paginator->items());

        return [
            'data' => $transform ? $items->map($transform)->values() : $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'from' => $paginator->firstItem(),   // baru
                'to' => $paginator->lastItem(),      // baru
            ],
        ];
    }
}
