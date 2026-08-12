<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDestroyAction;
use App\Http\Controllers\Concerns\HasIndexView;
use App\Http\Controllers\Concerns\HasShowAction;
use App\Http\Controllers\Concerns\HasStoreAction;
use App\Http\Controllers\Concerns\HasUpdateAction;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use HasIndexView, HasShowAction, HasStoreAction, HasUpdateAction, HasDestroyAction;

    public function __construct(protected PermissionService $permissionService)
    {
    }

    protected function service(): object
    {
        return $this->permissionService;
    }

    protected function viewName(): string
    {
        return 'pages.permissions.index';
    }

    // Override: PermissionService pakai method data(), bukan table()
    public function data(Request $request)
    {
        return response()->json($this->permissionService->data($request));
    }

    protected function storeRequestClass(): string
    {
        return StorePermissionRequest::class;
    }

    protected function updateRequestClass(): string
    {
        return UpdatePermissionRequest::class;
    }

    protected function messages(): array
    {
        return [
            'created' => 'Permission berhasil ditambahkan.',
            'updated' => 'Permission berhasil diperbarui.',
            'deleted' => 'Permission berhasil dihapus.',
        ];
    }
}