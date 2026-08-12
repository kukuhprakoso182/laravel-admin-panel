<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

trait ValidatesWithFormRequest
{
    protected function validateWith(Request $request, string $requestClass): array
    {
        if ($requestClass === Request::class) {
            return $request->all();
        }

        /** @var FormRequest $formRequest */
        $formRequest = app($requestClass);

        return $formRequest->validated();
    }

    protected function messages(): array
    {
        return [
            'created' => 'Data berhasil ditambahkan.',
            'updated' => 'Data berhasil diperbarui.',
            'deleted' => 'Data berhasil dihapus.',
        ];
    }
}
