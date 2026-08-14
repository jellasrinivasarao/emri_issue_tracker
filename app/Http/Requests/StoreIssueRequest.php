<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreIssueRequest extends FormRequest
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

            /*
            |--------------------------------------------------------------------------
            | Service Information
            |--------------------------------------------------------------------------
            */

            'state_id' => [
                'required',
                'integer',
                'exists:mst_state,state_id',
            ],

            'service_id' => [
                'nullable',
                'integer',
                'exists:mst_service,service_id',
            ],

            'project_id' => [
                'required',
                'integer',
                'exists:mst_project,project_id',
            ],

            'application_id' => [
                'required',
                'integer',
                'exists:mst_application,application_id',
            ],

            'module_id' => [
                'nullable',
                'integer',
                'exists:mst_module,module_id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Issue Information
            |--------------------------------------------------------------------------
            */

            'issue_category_id' => [
                'required',
                'integer',
            ],

            'priority_id' => [
                'required',
                'integer',
            ],

            'subject' => [
                'required',
                'string',
                'min:5',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Supporting Information
            |--------------------------------------------------------------------------
            */

            'occurred_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'occurred_time' => [
                'required',
                'date_format:H:i',
            ],

            'affected_users' => [
                'nullable',
                'string',
                'max:500',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
                'max:10240', // 10 MB
            ],

        ];
    }

    /**
     * Validation Messages
     */
    public function messages(): array
    {
        return [

            'state_id.required' => 'Please select a State.',
            'state_id.exists' => 'Selected State is invalid.',

            'service_id.exists' => 'Selected Service is invalid.',

            'project_id.required' => 'Please select a Project.',
            'project_id.exists' => 'Selected Project is invalid.',

            'application_id.required' => 'Please select an Application.',
            'application_id.exists' => 'Selected Application is invalid.',

            'module_id.exists' => 'Selected Module is invalid.',

            'issue_category_id.required' => 'Please select an Issue Category.',
            'priority_id.required' => 'Please select a Priority.',

            'subject.required' => 'Issue subject is required.',
            'subject.min' => 'Subject must contain at least 5 characters.',
            'subject.max' => 'Subject cannot exceed 255 characters.',

            'description.required' => 'Issue description is required.',
            'description.min' => 'Description must contain at least 10 characters.',
            'description.max' => 'Description cannot exceed 5000 characters.',

            'occurred_date.required' => 'Occurred date is required.',
            'occurred_date.before_or_equal' => 'Occurred date cannot be in the future.',

            'occurred_time.required' => 'Occurred time is required.',
            'occurred_time.date_format' => 'Invalid time format.',

            'attachment.mimes' => 'Allowed file types: jpg, jpeg, png, pdf, doc, docx, xls, xlsx.',
            'attachment.max' => 'Attachment size cannot exceed 10 MB.',

        ];
    }

    /**
     * Attribute Names
     */
    public function attributes(): array
    {
        return [

            'state_id' => 'State',
            'service_id' => 'Service',
            'project_id' => 'Project',
            'application_id' => 'Application',
            'module_id' => 'Module',
            'issue_category_id' => 'Issue Category',
            'priority_id' => 'Priority',
            'occurred_date' => 'Occurred Date',
            'occurred_time' => 'Occurred Time',
            'affected_users' => 'Affected Users',

        ];
    }

    /**
     * Prepare Data Before Validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'subject' => trim((string) $this->subject),

            'description' => trim((string) $this->description),

            'affected_users' => trim((string) $this->affected_users),

        ]);
    }

    /**
     * Handle Validation Failure
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {

            throw new HttpResponseException(

                response()->json([

                    'success' => false,

                    'message' => 'Validation failed.',

                    'errors' => $validator->errors(),

                ], 422)

            );

        }

        parent::failedValidation($validator);
    }
    
}