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
        $scheduleId = $this->route('working_schedule');

        return [
            'calendar_id' => [
                'required',
                'integer',
                //'exists:mst_calendar,calendar_id',
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

            'start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'end_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'is_working_day' => [
                'required',
                'boolean',
            ],

            'is_24_hours' => [
                'required',
                'boolean',
            ],

            'sequence_no' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],

            'effective_from' => [
                'nullable',
                'date',
            ],

            'effective_to' => [
                'nullable',
                'date',
                'after_or_equal:effective_from',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'break_start' => [
                'nullable',
                'date_format:H:i',
            ],

            'break_end' => [
                'nullable',
                'date_format:H:i',
                'after:break_start',
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
        ];
    }
}