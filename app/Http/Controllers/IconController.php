<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIconRequest;
use App\Http\Requests\UpdateIconRequest;
use App\Services\IconService;
use Illuminate\Http\Request;

class IconController extends Controller
{
    public function __construct(protected IconService $iconService)
    {
    }

    public function index()
    {
        return view('pages.icons.index');
    }

    public function data(Request $request)
    {
        return response()->json($this->iconService->table($request));
    }

    public function show(int|string $id)
    {
        return response()->json($this->iconService->find($id));
    }

    public function store(StoreIconRequest $request)
    {
        $icon = $this->iconService->create($request->validated());

        return response()->json([
            'message' => 'Icon berhasil ditambahkan.',
            'data' => $icon,
        ], 201);
    }

    public function update(UpdateIconRequest $request, int|string $id)
    {
        $icon = $this->iconService->update($id, $request->validated());

        return response()->json([
            'message' => 'Icon berhasil diperbarui.',
            'data' => $icon,
        ]);
    }

    public function destroy(int|string $id)
    {
        $this->iconService->delete($id);

        return response()->json(['message' => 'Icon berhasil dihapus.']);
    }
}
