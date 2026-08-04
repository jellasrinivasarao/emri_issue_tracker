<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrivilegeMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $privilegeId = $this->route('privilege_id');

        return [
            'privilege_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mst_privilege', 'privilege_code')->ignore($privilegeId, 'privilege_id'),
            ],
            'privilege_name' => ['required', 'string', 'max:150'],
            'module_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'privilege_code' => 'Privilege Code',
            'privilege_name' => 'Privilege Name',
            'module_name' => 'Module Name',
            'description' => 'Description',
        ];
    }
}
