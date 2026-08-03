<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\Exportable;
use App\Support\TableResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\CsvExporter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserService implements Exportable
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    public function list(int $perPage = 15)
    {
        return $this->userRepository->paginate($perPage);
    }

    public function find(int|string $id)
    {
        return $this->userRepository->find($id)->load('roles');
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $roleIds = $data['roles'] ?? [];
        unset($data['roles']);

        $user = $this->userRepository->create($data);

        if (!empty($roleIds)) {
            $this->userRepository->syncRoles($user->id, $roleIds);
        }

        return $user->load('roles');
    }

    public function update(int|string $id, array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $roleIds = $data['roles'] ?? null;
        unset($data['roles']);

        $user = $this->userRepository->update($id, $data);

        if ($roleIds !== null) {
            $this->userRepository->syncRoles($id, $roleIds);
        }

        return $user->load('roles');
    }

    public function delete(int|string $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function table(Request $request): array
    {
        $query = $this->baseQuery($request);

        $paginated = $this->userRepository->paginateFiltered(
            request: $request,
            searchableColumns: ['name', 'email'],
            sortableColumns: ['name', 'email', 'status', 'created_at'],
            query: $query,
        );

        return TableResponseFormatter::format($paginated, fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'created_at' => $user->created_at,
            'roles' => $user->roles->map(fn ($r) => ['id' => $r->id, 'name' => $r->name]),
        ]);
    }

    protected function baseQuery(Request $request)
    {
        $query = $this->userRepository->query()->with('roles');

        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('roles.id', $request->get('role')));
        }

        return $query;
    }


    public function export(Request $request): StreamedResponse
    {
        $query = $this->baseQuery($request);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return CsvExporter::stream(
            rows: $users,
            headers: ['Nama', 'Email', 'Role', 'Status', 'Bergabung'],
            mapRow: fn ($user) => [
                $user->name,
                $user->email,
                $user->roles->pluck('name')->join(', '),
                $user->status === 'active' ? 'Aktif' : 'Nonaktif',
                optional($user->created_at)->format('Y-m-d H:i'),
            ],
            filenamePrefix: 'users',
        );
    }
}
