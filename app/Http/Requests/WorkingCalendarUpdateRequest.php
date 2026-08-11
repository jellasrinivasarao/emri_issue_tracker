<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkingCalendarUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'calendar_code' => ['required', 'string', 'max:60'],
            'calendar_name' => ['required', 'string', 'max:191'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'organisation_id' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
