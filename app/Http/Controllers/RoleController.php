<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService, protected PermissionService $permissionService)
    {
    }

    public function index()
    {
        return view('pages.roles.index', [
            'permissions' => $this->permissionService->allForOptions(),
        ]);
    }

    public function data(Request $request)
    {
        return response()->json($this->roleService->table($request));
    }

    public function show(int|string $id)
    {
        return response()->json($this->roleService->find($id));
    }

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->create($request->validated());

        return response()->json([
            'message' => 'Role berhasil ditambahkan.',
            'data' => $role,
        ], 201);
    }

    public function update(UpdateRoleRequest $request, int|string $id)
    {
        $role = $this->roleService->update($id, $request->validated());

        return response()->json([
            'message' => 'Role berhasil diperbarui.',
            'data' => $role,
        ]);
    }

    public function destroy(int|string $id)
    {
        $this->roleService->delete($id);

        return response()->json(['message' => 'Role berhasil dihapus.']);
    }

    public function syncPermissions(Request $request, int|string $id)
    {
        $data = $request->validate([
            'menu_permissions' => ['required', 'array'],
            'menu_permissions.*.menu_id' => ['required', 'exists:menus,id'],
            'menu_permissions.*.permission_id' => ['required', 'exists:permissions,id'],
        ]);

        return response()->json(
            $this->roleService->assignMenuPermissions($id, $data['menu_permissions'])
        );
    }

    public function permissionMatrix(int|string $id)
    {
        return response()->json($this->roleService->menuPermissionMatrix($id));
    }
}
