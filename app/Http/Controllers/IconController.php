<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIconRequest;
use App\Http\Requests\UpdateIconRequest;
use App\Services\IconService;

class IconController extends BaseCrudController
{
    public function __construct(protected IconService $iconService)
    {
    }

    protected function service(): object
    {
        return $this->iconService;
    }

    protected function viewName(): string
    {
        return 'pages.icons.index';
    }

    protected function storeRequestClass(): string
    {
        return StoreIconRequest::class;
    }

    protected function updateRequestClass(): string
    {
        return UpdateIconRequest::class;
    }

    protected function messages(): array
    {
        return [
            'created' => 'Icon berhasil ditambahkan.',
            'updated' => 'Icon berhasil diperbarui.',
            'deleted' => 'Icon berhasil dihapus.',
        ];
    }
}