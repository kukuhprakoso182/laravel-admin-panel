<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(protected PermissionService $permissionService) {}

    public function index()
    {
        return view('pages.permissions.index');
    }

    public function data(Request $request)
    {
        return response()->json($this->permissionService->data($request));
    }

    public function show(int|string $id)
    {
        return response()->json($this->permissionService->find($id));
    }

    public function store(StorePermissionRequest $request)
    {
        return response()->json([
            'message' => 'Permission berhasil ditambahkan.',
            'data' => $this->permissionService->create($request->validated()),
        ], 201);
    }

    public function update(UpdatePermissionRequest $request, int|string $id)
    {
        return response()->json([
            'message' => 'Permission berhasil diperbarui.',
            'data' => $this->permissionService->update($id, $request->validated()),
        ]);
    }

    public function destroy(int|string $id)
    {
        $this->permissionService->delete($id);
        return response()->json(['message' => 'Permission berhasil dihapus']);
    }
}
