<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'vendor_name' => ['required', 'string', 'max:255'],
            'vendor_category' => ['required', 'string', 'max:255'],
            'vendor_contact_person' => ['required', 'string', 'max:255'],
            'primary_contact_mobile' => [
                'nullable',
                'string',
                'regex:/^(?:[0-9]{10}|\\+91[0-9]{10})$/',
            ],
            'primary_contact_email' => ['nullable', 'email', 'max:150'],
            'support_mobile' => [
                'nullable',
                'string',
                'regex:/^(?:[0-9]{10}|\\+91[0-9]{10})$/',
            ],
            'vendor_description' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'vendor_contact_person' => 'Primary Contact Person',
            'primary_contact_mobile' => 'Primary Contact Mobile',
            'primary_contact_email' => 'Primary Contact Email',
            'support_mobile' => 'Support Mobile',
            'vendor_description' => 'Description',
        ];
    }
}
