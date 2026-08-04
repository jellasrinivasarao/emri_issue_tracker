<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $project = $this->route('project');

        $projectId = $project
            ? $project->project_id
            : null;

            return [

            'state_id' => [
                'required',
                'integer',
                'exists:mst_state,state_id',
            ],

            'project_code' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'mst_project',
                    'project_code'
                )->ignore(
                    $projectId,
                    'project_id'
                ),
            ],

            'project_name' => [
                'required',
                'string',
                'max:200',
            ],

            'project_description' => [
                'nullable',
                'string',
            ],'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'project_status' => [
                'required',
                'string',
                'max:30',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'state_id' => 'State',
            'project_code' => 'Project Code',
            'project_name' => 'Project Name',
            'project_description' => 'Project Description',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'project_status' => 'Project Status',
            'is_active' => 'Active',
        ];
    }
}