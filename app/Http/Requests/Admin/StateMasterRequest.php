<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StateMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'state_name' => ['required', 'string', 'max:255'],
            'state_code' => ['required', 'string', 'max:50'],
            'state_short_name' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function attributes(): array
    {
        return [
            'state_name' => 'State Name',
            'state_code' => 'State Code',
            'state_short_name' => 'Short Name',
        ];
    }
}
