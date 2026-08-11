<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class WorkingCalendarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $calendarId = $this->route('working_calendar')?->working_calendar_id
            ?? $this->route('working_calendar');

        return [
            'calendar_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mst_working_calendar', 'calendar_code')
                    ->ignore($calendarId, 'working_calendar_id'),
            ],

            'calendar_name' => [
                'required',
                'string',
                'max:150',
            ],

            'timezone' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
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
            'calendar_code' => 'calendar code',
            'calendar_name' => 'calendar name',
            'timezone' => 'timezone',
            'is_active' => 'status',
        ];
    }
}