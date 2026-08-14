<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProjectApplicationModuleMappingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:mst_project,project_id'],
            'application_ids' => ['required', 'array', 'min:1'],
            'application_ids.*' => ['required', 'integer', 'distinct', 'exists:mst_application,application_id'],
            'module_ids' => ['required', 'array', 'min:1'],
            'module_ids.*' => ['required', 'integer', 'distinct', 'exists:mst_module,module_id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'project_id' => 'Project',
            'application_ids' => 'Applications',
            'application_ids.*' => 'Application',
            'module_ids' => 'Modules',
            'module_ids.*' => 'Module',
        ];
    }
}
