<?php

namespace App\Http\Controllers\Concerns;

trait HasDestroyAction
{
    use ValidatesWithFormRequest;

    abstract protected function service(): object;

    public function destroy(int|string $id)
    {
        $this->service()->delete($id);

        return response()->json(['message' => $this->messages()['deleted']]);
    }
}
