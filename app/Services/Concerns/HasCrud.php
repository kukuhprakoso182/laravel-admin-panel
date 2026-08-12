<?php

namespace App\Services\Concerns;

trait HasCrud
{
    abstract protected function repository(): object;

    public function find(int|string $id)
    {
        return $this->repository()->find($id);
    }

    public function create(array $data)
    {
        return $this->repository()->create($data);
    }

    public function update(int|string $id, array $data)
    {
        return $this->repository()->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        return $this->repository()->delete($id);
    }

    public function list(int $perPage = 15)
    {
        return $this->repository()->paginate($perPage);
    }
}
