<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupProjectApplicationMappingRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $projectValue = $this->input('project_id');
        $project = $projectValue
            ? \Illuminate\Support\Facades\DB::table('mst_project')
                ->where('project_id', $projectValue)
                ->orWhere('project_name', (string) $projectValue)
                ->first(['project_id'])
            : null;

        if ($project) {
            $this->merge(['project_id' => $project->project_id]);
        }
    }

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'group_id' => ['required', 'integer', 'exists:mst_group,group_id'],
            'project_id' => ['required', 'integer', 'exists:mst_project,project_id'],
            'application_ids' => ['required', 'array', 'min:1'],
            'application_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('map_project_application_module', 'application_id')->where(fn ($query) => $query
                    ->where('project_id', $this->input('project_id'))
                    ->where('is_active', 1)),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'group_id' => 'Group',
            'project_id' => 'Project',
            'application_ids' => 'Applications',
            'application_ids.*' => 'Application',
        ];
    }
}