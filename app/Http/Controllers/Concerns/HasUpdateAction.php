<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait HasUpdateAction
{
    use ValidatesWithFormRequest;

    abstract protected function service(): object;

    protected function updateRequestClass(): string
    {
        return Request::class;
    }

    public function update(Request $request, int|string $id)
    {
        $validated = $this->validateWith($request, $this->updateRequestClass());

        $data = $this->service()->update($id, $validated);

        return response()->json([
            'message' => $this->messages()['updated'],
            'data' => $data,
        ]);
    }
}
