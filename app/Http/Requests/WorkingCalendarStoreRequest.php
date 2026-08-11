<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkingCalendarStoreRequest extends FormRequest
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
            'state_id' => ['nullable', 'integer', 'exists:mst_state,state_id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
