<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'employee_code' => ['nullable', 'string', 'max:50'],
            'user_name' => ['required', 'string', 'max:100'],
            'login_id' => ['nullable', 'string', 'max:100'],
            'official_email' => ['required', 'string', 'email', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:25'],
            'role_id' => ['required', 'integer'],
            'password' => ['nullable', 'string', 'min:6'],
            'user_status' => ['required', 'string', 'max:50'],
        ];
    }
}
