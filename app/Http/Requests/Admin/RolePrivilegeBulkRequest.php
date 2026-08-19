<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RolePrivilegeBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'role_id' => ['required', 'integer', 'exists:mst_role,role_id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['array'],
            'permissions.*.*' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'role_id' => 'Role',
            'permissions' => 'Permissions',
        ];
    }
}
