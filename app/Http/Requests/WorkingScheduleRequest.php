<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkingScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'calendar_id' => [
                'required',
                'integer',
                // 'exists:mst_calendar,calendar_id',
            ],

            'day_of_week' => [
                'required',
                'string',
                Rule::in([
                    'MONDAY',
                    'TUESDAY',
                    'WEDNESDAY',
                    'THURSDAY',
                    'FRIDAY',
                    'SATURDAY',
                    'SUNDAY',
                ]),
            ],

            'schedule_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'shift_no' => [
                'nullable',
                'integer',
                'min:1',
                'max:255',
            ],

            'shift_name' => [
                'nullable',
                'string',
                'max:50',
            ],

            'sequence_no' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],

            /*
            |--------------------------------------------------------------------------
            | Working Hours
            |--------------------------------------------------------------------------
            */

            'start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'end_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'break_start' => [
                'nullable',
                'date_format:H:i',
                'required_with:break_end',
            ],

            'break_end' => [
                'nullable',
                'date_format:H:i',
                'required_with:break_start',
    'after:break_start',
            ],

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'effective_from' => [
                'nullable',
                'date',
            ],

            'effective_to' => [
                'nullable',
                'date',
                'after_or_equal:effective_from',
            ],

            /*
            |--------------------------------------------------------------------------
            | Boolean Fields
            |--------------------------------------------------------------------------
            */

            'is_working_day' => [
                'required',
                'boolean',
            ],

            'is_24_hours' => [
                'required',
                'boolean',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_working_day' => $this->boolean('is_working_day'),
            'is_24_hours'    => $this->boolean('is_24_hours'),
            'is_active'      => $this->boolean('is_active'),
        ]);
    }
}