<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SupportGroupMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'support_group_name' => ['required', 'string', 'max:255'],
            'support_group_lead' => ['nullable', 'string', 'max:255'],
            'support_group_escalation_level' => ['nullable', 'string', 'max:255'],
            'support_group_description' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'support_group_name' => 'Support Group Name',
            'support_group_lead' => 'Lead',
            'support_group_escalation_level' => 'Escalation Level',
            'support_group_description' => 'Description',
        ];
    }
}
