<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SlaConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sla = $this->route('sla_configuration');

        $slaId = $sla instanceof \App\Models\SlaConfiguration
            ? $sla->sla_configuration_id
            : $sla;

        return [
            'sla_code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique(
                    'mst_sla_configuration',
                    'sla_code'
                )->ignore(
                    $slaId,
                    'sla_configuration_id'
                ),
            ],

            'sla_name' => [
                'required',
                'string',
                'max:150',
            ],

            'support_level' => [
                'required',
                'integer',
                Rule::in([1, 2]),
            ],

            'response_sla_hours' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'resolution_sla_hours' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'escalation_sla_hours' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'calendar_id' => [
                'required',
                'integer',
                'exists:mst_working_calendar,calendar_id',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $response = (float) $this->response_sla_hours;
            $resolution = (float) $this->resolution_sla_hours;
            $escalation = $this->escalation_sla_hours !== null
                ? (float) $this->escalation_sla_hours
                : null;

            if ($resolution > 0 && $response > $resolution) {
                $validator->errors()->add(
                    'response_sla_hours',
                    'Response SLA cannot be greater than Resolution SLA.'
                );
            }

            if ($escalation !== null && $escalation > $resolution) {
                $validator->errors()->add(
                    'escalation_sla_hours',
                    'Escalation SLA cannot be greater than Resolution SLA.'
                );
            }
        });
    }
}