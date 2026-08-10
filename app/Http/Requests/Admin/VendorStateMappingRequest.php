<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VendorStateMappingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'vendor_id' => ['required', 'integer', 'exists:mst_vendor,vendor_id'],
            'state_ids' => ['required', 'array', 'min:1'],
            'state_ids.*' => ['required', 'integer', 'distinct', 'exists:mst_state,state_id'],
            'project_ids' => ['required', 'array', 'min:1'],
            'project_ids.*' => ['required', 'integer', 'distinct', 'exists:mst_project,project_id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'vendor_id' => 'Vendor',
            'state_ids' => 'State',
            'state_ids.*' => 'State',
            'project_ids' => 'Project',
            'project_ids.*' => 'Project',
        ];
    }
}
