<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'short_code' => ['nullable', 'string', 'max:100'],
            'service_name' => ['required', 'string', 'max:255'],
            'service_description' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'short_code' => 'Short Code',
            'service_name' => 'Service Name',
            'service_description' => 'Description',
        ];
    }
}
