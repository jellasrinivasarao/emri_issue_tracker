<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganisationRequest extends FormRequest
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
        $organisation = $this->route('organisation');

        $organisationId = $organisation
            ? $organisation->organisation_id
            : null;

        return [

            'organisation_type_id' => [
                'required',
                'integer',
                'exists:mst_organisation_type,organisation_type_id',
            ],

            'organisation_code' => [
                'required',
                'string',
                'max:30',

                Rule::unique(
                    'mst_organisation',
                    'organisation_code'
                )->ignore(
                    $organisationId,
                    'organisation_id'
                ),
            ],
            'organisation_name' => [
                'required',
                'string',
                'max:200',
            ],

            'short_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'mobile' => [
                'nullable',
                'string',
                'max:20',
            ],
            'address_line1' => [
                'nullable',
                'string',
                'max:250',
            ],

            'address_line2' => [
                'nullable',
                'string',
                'max:250',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'required',
                'string',
                'max:100',
            ],

            'pincode' => [
                'nullable',
                'string',
                'max:20',
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
            'organisation_type_id'
                => 'Organisation Type',

            'organisation_code'
                => 'Organisation Code',

            'organisation_name'
                => 'Organisation Name',

            'short_name'
                => 'Short Name',

            'address_line1'
                => 'Address Line 1',

            'address_line2'
                => 'Address Line 2',

            'state_name'
                => 'State',

            'is_active'
                => 'Active',
        ];
    }
    
}