<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $groupId = $this->route('group_id');

        return [
            'group_name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('mst_group', 'group_name')->ignore($groupId, 'group_id'),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'group_name' => 'Group Name',
            'description' => 'Description',
            'is_active' => 'Active',
        ];
    }
}