<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;
use Illuminate\Validation\Rule;

class UserMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $userId = $this->route('user_id') ?? $this->input('user_id') ?? null;

        $roleId = $this->input('role_id');
        $roleName = null;
        if ($roleId) {
            $roleName = Role::where('role_id', $roleId)->value('role_name');
        }

        $stateRequired = $roleName && str_contains(strtolower($roleName), 'state');
        $vendorRequired = $roleName && str_contains(strtolower($roleName), 'vendor');

        return [
            'employee_code' => ['nullable', 'string', 'max:50'],
            'user_name' => ['required', 'string', 'max:100'],
            'login_id' => ['nullable', 'string', 'max:100'],
            'official_email' => array_filter([
                'required',
                'string',
                'email',
                'max:255',
                $userId ? Rule::unique('mst_user', 'official_email')->ignore($userId, 'user_id') : Rule::unique('mst_user', 'official_email'),
            ]),
            'mobile_number' => ['nullable', 'regex:/^[0-9]{10}$/'],
            'role_id' => ['required', 'integer', 'exists:mst_role,role_id'],
            'password' => $this->isMethod('post') ? ['required', 'string', 'min:6'] : ['nullable', 'string', 'min:6'],
            'user_status' => ['nullable', 'string', 'max:50'],
            'state_id' => ['nullable', 'integer', 'exists:mst_state,state_id'],
            'state_ids' => $stateRequired ? ['required', 'array', 'min:1'] : ['nullable', 'array'],
            'state_ids.*' => ['integer', 'exists:mst_state,state_id'],
            'vendor_id' => $vendorRequired ? ['required', 'integer', 'exists:mst_vendor,vendor_id'] : ['nullable', 'integer', 'exists:mst_vendor,vendor_id'],
        ];
    }
}
