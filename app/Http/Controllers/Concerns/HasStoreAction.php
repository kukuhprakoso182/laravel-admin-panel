<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait HasStoreAction
{
    use ValidatesWithFormRequest;

    abstract protected function service(): object;

    protected function storeRequestClass(): string
    {
        return Request::class;
    }

    public function store(Request $request)
    {
        $validated = $this->validateWith($request, $this->storeRequestClass());

        $data = $this->service()->create($validated);

        return response()->json([
            'message' => $this->messages()['created'],
            'data' => $data,
        ], 201);
    }
}
