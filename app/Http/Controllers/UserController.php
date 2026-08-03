<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected RoleService $roleService,
    ) {
    }

    public function index()
    {
        return view('pages.users.index', [
            'roles' => $this->roleService->allForOptions(),
        ]);
    }

    public function data(Request $request)
    {
        return response()->json($this->userService->table($request));
    }

    public function show(int|string $id)
    {
        return response()->json($this->userService->find($id));
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return response()->json([
            'message' => 'User berhasil ditambahkan.',
            'data' => $user,
        ], 201);
    }

    public function update(UpdateUserRequest $request, int|string $id)
    {
        $user = $this->userService->update($id, $request->validated());

        return response()->json([
            'message' => 'User berhasil diperbarui.',
            'data' => $user,
        ]);
    }

    public function destroy(int|string $id)
    {
        $this->userService->delete($id);

        return response()->json(['message' => 'User berhasil dihapus.']);
    }

    public function export(Request $request)
    {
        return $this->userService->export($request);
    }
}
