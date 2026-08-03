<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Services\IconService;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(
        protected MenuService $menuService,
        protected IconService $iconService,
    ) {
    }

    public function index()
    {
        return view('pages.menus.index', [
            'parentMenus' => $this->menuService->allForOptions(),
            'icons' => $this->iconService->allForOptions(),
        ]);
    }

    public function data(Request $request)
    {
        return response()->json($this->menuService->table($request));
    }

    public function tree()
    {
        return response()->json($this->menuService->tree());
    }

    public function show(int|string $id)
    {
        return response()->json($this->menuService->find($id));
    }

    public function store(StoreMenuRequest $request)
    {
        $menu = $this->menuService->create($request->validated());

        return response()->json([
            'message' => 'Menu berhasil ditambahkan.',
            'data' => $menu,
        ], 201);
    }

    public function update(UpdateMenuRequest $request, int|string $id)
    {
        $menu = $this->menuService->update($id, $request->validated());

        return response()->json([
            'message' => 'Menu berhasil diperbarui.',
            'data' => $menu,
        ]);
    }

    public function destroy(int|string $id)
    {
        $this->menuService->delete($id);

        return response()->json(['message' => 'Menu berhasil dihapus.']);
    }
}
