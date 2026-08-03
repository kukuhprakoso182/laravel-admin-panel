<?php

namespace App\Http\Requests;

class UpdateMenuRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $menuId = $this->route('id');

        return [
            'parent_id' => [
                'nullable',
                'exists:menus,id',
                function ($attribute, $value, $fail) use ($menuId) {
                    if ($value && (string) $value === (string) $menuId) {
                        $fail('Menu tidak boleh menjadi parent untuk dirinya sendiri.');
                    }
                },
            ],
            'icon_id' => ['nullable', 'exists:icons,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:255'],
            'link_alias' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
