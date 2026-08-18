<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateVendorRequirementRequest extends FormRequest
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

            'man_days' => [
                'required',
                'numeric',
                'min:0',
            ],

            'timeline' => [
                'required',
                'string',
            ],

            'delivery_status' => [
                'required',
                Rule::in([

                    'Requirements Understood',

                    'Development Started',

                    'Development Completed',

                    'Moved to UAT',

                    'UAT Completed',

                    'Moved to Production',

                    'On Hold',
                ]),
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
