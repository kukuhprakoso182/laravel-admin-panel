<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends BaseCrudController
{
    public function __construct(
        protected UserService $userService,
        protected RoleService $roleService,
    ) {
    }

    protected function service(): object
    {
        return $this->userService;
    }

    protected function viewName(): string
    {
        return 'pages.users.index';
    }

    // Override: index butuh data role untuk dropdown
    public function index()
    {
        return view($this->viewName(), [
            'roles' => $this->roleService->allForOptions(),
        ]);
    }

    protected function storeRequestClass(): string
    {
        return StoreUserRequest::class;
    }

    protected function updateRequestClass(): string
    {
        return UpdateUserRequest::class;
    }

    protected function messages(): array
    {
        return [
            'created' => 'User berhasil ditambahkan.',
            'updated' => 'User berhasil diperbarui.',
            'deleted' => 'User berhasil dihapus.',
        ];
    }

    public function export(Request $request)
    {
        return $this->userService->export($request);
    }
}