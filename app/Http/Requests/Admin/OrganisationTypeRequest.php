<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganisationTypeRequest extends FormRequest
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
        $id = $this->route('organisation_type')
            ? $this->route('organisation_type')->organisation_type_id
            : null;

        return [
            'organisation_type_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique(
                    'mst_organisation_type',
                    'organisation_type_code'
                )->ignore(
                    $id,
                    'organisation_type_id'
                ),
            ],

            'organisation_type_name' => [
                'required',
                'string',
                'max:150',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'is_active' => [
                'boolean',
            ],
        ];
    }
}