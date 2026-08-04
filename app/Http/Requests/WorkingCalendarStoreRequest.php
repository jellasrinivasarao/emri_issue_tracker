<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkingCalendarStoreRequest extends FormRequest
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
        return [
            'calendar_code' => [
                'required',
                'string',
                'max:50',
                'unique:mst_working_calendar,calendar_code',
            ],

            'calendar_name' => [
                'required',
                'string',
                'max:150',
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'organisation_id' => [
                'nullable',
                'integer',
            ],

            'timezone' => [
                'required',
                'timezone',
            ],

            'effective_from' => [
                'nullable',
                'date',
            ],
            'effective_to' => [
                'nullable',
                'date',
                'after_or_equal:effective_from',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

}