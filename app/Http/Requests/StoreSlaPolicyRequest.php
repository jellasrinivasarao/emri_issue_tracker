<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSlaPolicyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'project_id'=>[
                'required',
                'exists:mst_project,project_id'
            ],

            'application_id'=>[
                'required',
                'exists:mst_application,application_id'
            ],

            'service_id'=>[
                'required',
                'exists:mst_service,service_id'
            ],

            'priority_id'=>[
                'required',
                'exists:mst_priority,priority_id'
            ],

            'calendar_id'=>[
                'required',
                'exists:mst_working_calendar,calendar_id'
            ],
             'response_time_minutes'=>[
                'required',
                'integer',
                'min:1'
            ],

            'resolution_time_minutes'=>[
                'required',
                'integer',
                'gt:response_time_minutes'
            ],

            'warning_before_minutes'=>[
                'nullable',
                'integer',
                'min:0'
            ],

            'auto_escalation'=>[
                'boolean'
            ],

            'description'=>[
                'nullable',
                'string'
            ],
            'is_active'=>[
                'boolean'
            ],

        ];
    }
}