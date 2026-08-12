<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\Exportable;
use App\Support\CsvExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserService extends BaseService implements Exportable
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
        parent::__construct($userRepository);
    }

    protected function repository(): object
    {
        return $this->userRepository;
    }

    public function find(int|string $id)
    {
        return $this->userRepository->find($id)->load('roles');
    }

    // Override: create butuh hash password + sync roles
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

    protected function baseQuery(Request $request): Builder
    {
        $query = parent::baseQuery($request)->with('roles');

        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('roles.id', $request->get('role')));
        }

        return $query;
    }

    protected function searchableColumns(): array
    {
        return ['name', 'email'];
    }

    protected function sortableColumns(): array
    {
        return ['name', 'email', 'status', 'created_at'];
    }

    protected function formatRow(mixed $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'email' => $item->email,
            'status' => $item->status,
            'created_at' => $item->created_at,
            'roles' => $item->roles->map(fn ($r) => ['id' => $r->id, 'name' => $r->name]),
        ];
    }
}
