<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends BaseCrudController
{
    public function __construct(
        protected RoleService $roleService,
        protected PermissionService $permissionService,
    ) {
    }

    protected function service(): object
    {
        return $this->roleService;
    }

    protected function viewName(): string
    {
        return 'pages.roles.index';
    }

    // Override: index butuh data permission untuk form
    public function index()
    {
        return view($this->viewName(), [
            'permissions' => $this->permissionService->allForOptions(),
        ]);
    }

    protected function storeRequestClass(): string
    {
        return StoreRoleRequest::class;
    }

    protected function updateRequestClass(): string
    {
        return UpdateRoleRequest::class;
    }

    protected function messages(): array
    {
        return [
            'created' => 'Role berhasil ditambahkan.',
            'updated' => 'Role berhasil diperbarui.',
            'deleted' => 'Role berhasil dihapus.',
        ];
    }

    // Method khusus Role, tidak ada di base
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