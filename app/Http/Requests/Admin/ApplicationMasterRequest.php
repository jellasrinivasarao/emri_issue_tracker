<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'application_name' => ['required', 'string', 'max:255'],
            'application_owner' => ['nullable', 'string', 'max:255'],
            'application_version' => ['nullable', 'string', 'max:100'],
            'application_description' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'application_name' => 'Application Name',
            'application_owner' => 'Owner',
            'application_version' => 'Version',
            'application_description' => 'Description',
        ];
    }
}
