<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Services\IconService;
use App\Services\MenuService;

class MenuController extends BaseCrudController
{
    public function __construct(
        protected MenuService $menuService,
        protected IconService $iconService,
    ) {
    }

    protected function service(): object
    {
        return $this->menuService;
    }

    protected function viewName(): string
    {
        return 'pages.menus.index';
    }

    public function index()
    {
        return view($this->viewName(), [
            'parentMenus' => $this->menuService->allForOptions(),
            'icons' => $this->iconService->allForOptions(),
        ]);
    }

    // Method khusus Menu, tidak ada di base
    public function tree()
    {
        return response()->json($this->menuService->tree());
    }

    protected function storeRequestClass(): string
    {
        return StoreMenuRequest::class;
    }

    protected function updateRequestClass(): string
    {
        return UpdateMenuRequest::class;
    }

    protected function messages(): array
    {
        return [
            'created' => 'Menu berhasil ditambahkan.',
            'updated' => 'Menu berhasil diperbarui.',
            'deleted' => 'Menu berhasil dihapus.',
        ];
    }
}