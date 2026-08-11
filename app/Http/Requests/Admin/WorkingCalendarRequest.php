<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkingCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $calendarId = $this->route('calendar_id');

        return [
            'calendar_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mst_working_calendar', 'calendar_code')
                    ->ignore($calendarId, 'calendar_id'),
            ],

            'calendar_name' => [
                'required',
                'string',
                'max:150',
            ],

            'organisation_id' => [
                'required',
                'integer',
                'exists:mst_organisation,organisation_id',
            ],

            'timezone' => [
                'required',
                'string',
                'max:100',
                'timezone',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'calendar_code.required' => 'Calendar code is required.',
            'calendar_code.unique' => 'Calendar code already exists.',

            'calendar_name.required' => 'Calendar name is required.',

            'organisation_id.required' => 'Organisation is required.',
            'organisation_id.exists' => 'Selected organisation does not exist.',

            'timezone.required' => 'Timezone is required.',
            'timezone.timezone' => 'Please select a valid timezone.',
        ];
    }
}