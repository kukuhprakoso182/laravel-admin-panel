<?php

namespace App\Http\Controllers\Concerns;

trait HasShowAction
{
    abstract protected function service(): object;

    public function show(int|string $id)
    {
        return response()->json($this->service()->find($id));
    }
}
