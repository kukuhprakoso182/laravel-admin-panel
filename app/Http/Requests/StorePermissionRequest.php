<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StorePermissionRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('permissions', 'name')],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Permission dengan nama ini sudah ada.',
        ];
    }
}
