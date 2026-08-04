<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        #return false;
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $state = $this->route('state');

        $stateId = $state
            ? $state->state_id
            : null;

        return [
            'organisation_id' => [
                'required',
                'integer',
                'exists:mst_organisation,organisation_id',
            ],

            'state_code' => [
                'required',
                'string',
                'max:50',
                'unique:mst_state,state_code,' .
                    $stateId .
                    ',state_id',
            ],

            'state_name' => [
                'required',
                'string',
                'max:150',
            ],

            'state_short_name' => [
                'nullable',
                'string',
                'max:20',
            ],
            
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
        
    }
    public function attributes(): array
    {
        return [
            'organisation_id' => 'Organisation',
            'state_code' => 'State Code',
            'state_name' => 'State Name',
            'state_short_name' => 'Short Name',
            'is_active' => 'Status',
        ];
    }
}