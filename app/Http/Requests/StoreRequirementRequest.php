<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequirementRequest extends FormRequest
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

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'state_id' => [
                'required',
                'integer',
                'exists:states,id',
            ],

            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
            ],

            'brd_raised_by' => [
                'nullable',
                'string',
                'max:255',
            ],

            'ho_it_team' => [
                'required',
                'boolean',
            ],

            'brd_document' => [
                'required',
                'file',
                'mimes:pdf,docx,xlsx',
                'max:25600',
            ],

            'received_at' => [
                'nullable',
                'date',
            ],

            'requested_to_vendor_at' => [
                'nullable',
                'date',
            ],

            'additional_details' => [
                'nullable',
                'string',
            ],
        ];
    }
}
