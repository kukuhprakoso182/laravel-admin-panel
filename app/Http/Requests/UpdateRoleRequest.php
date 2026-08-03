<?php

namespace App\Http\Requests;

class UpdateRoleRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:255', 'unique:roles,name,' . $roleId],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
