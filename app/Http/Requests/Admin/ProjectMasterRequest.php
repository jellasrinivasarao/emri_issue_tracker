<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProjectMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'project_name' => ['required', 'string', 'max:255'],
            'short_code' => ['nullable', 'string', 'max:100'],
            'project_description' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'project_name' => 'Project Name',
            'short_code' => 'Short Code',
            'project_description' => 'Description',
        ];
    }
}
