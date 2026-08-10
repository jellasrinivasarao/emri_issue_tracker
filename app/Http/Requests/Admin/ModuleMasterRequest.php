<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ModuleMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'module_name' => ['required', 'string', 'max:255'],
            'module_application' => ['nullable', 'string', 'max:255'],
            'module_owner' => ['nullable', 'string', 'max:255'],
            'module_description' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'module_name' => 'Module Name',
            'module_application' => 'Application',
            'module_owner' => 'Owner',
            'module_description' => 'Description',
        ];
    }
}
