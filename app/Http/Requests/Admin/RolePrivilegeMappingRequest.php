<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RolePrivilegeMappingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'role_id' => ['required', 'integer', 'exists:mst_role,role_id'],
            'privilege_id' => ['required', 'integer', 'exists:mst_privilege,privilege_id'],
            'is_allowed' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'role_id' => 'Role',
            'privilege_id' => 'Privilege',
            'is_allowed' => 'Allowed',
        ];
    }
}
