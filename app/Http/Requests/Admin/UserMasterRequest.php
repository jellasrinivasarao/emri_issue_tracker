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

        $normalizedRoleName = strtolower(trim(preg_replace('/\s+/', ' ', (string) $roleName)));
        $stateRequired = $normalizedRoleName && (
            str_contains($normalizedRoleName, 'state')
            || str_contains($normalizedRoleName, 'ho it')
        );
        $vendorRequired = $normalizedRoleName && str_contains($normalizedRoleName, 'vendor');
        $currentUserIsVendorAdmin = auth()->user()?->hasRole('Vendor Admin');

        return [
            'employee_code' => ['nullable', 'string', 'max:50'],
            'user_name' => array_filter([
                'required',
                'string',
                'max:100',
                $userId ? Rule::unique('mst_user', 'user_name')->ignore($userId, 'user_id') : Rule::unique('mst_user', 'user_name'),
            ]),
            'login_id' => array_filter([
                'nullable',
                'string',
                'max:100',
                $userId ? Rule::unique('mst_user', 'login_id')->ignore($userId, 'user_id') : Rule::unique('mst_user', 'login_id'),
            ]),
            'official_email' => array_filter([
                'required',
                'string',
                'email',
                'max:255',
                $userId ? Rule::unique('mst_user', 'official_email')->ignore($userId, 'user_id') : Rule::unique('mst_user', 'official_email'),
            ]),
            'mobile_number' => ['nullable', 'regex:/^[0-9]{10}$/'],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('mst_role', 'role_id'),
            ],
            'password' => $this->isMethod('post') ? ['required', 'string', 'min:6'] : ['nullable', 'string', 'min:6'],
            'user_status' => ['nullable', 'string', 'max:50'],
            'state_id' => [
                'nullable',
                'integer',
                Rule::exists('mst_state', 'state_id')->where(fn ($query) => $query->where('is_active', 1)),
            ],
            'state_ids' => $stateRequired ? ['required', 'array', 'min:1'] : ['nullable', 'array'],
            'state_ids.*' => [
                'integer',
                Rule::exists('mst_state', 'state_id')->where(fn ($query) => $query->where('is_active', 1)),
            ],
            'vendor_id' => array_merge(
                $vendorRequired && ! $currentUserIsVendorAdmin ? ['required'] : ['nullable'],
                [
                    'integer',
                    Rule::exists('mst_vendor', 'vendor_id')->where(fn ($query) => $query->where('is_active', 1)),
                ]
            ),
        ];
    }

    public function attributes(): array
    {
        return [
            'user_name' => 'Username',
            'login_id' => 'Login ID',
            'official_email' => 'Official email',
        ];
    }

    public function messages(): array
    {
        return [
            'user_name.unique' => 'This username already exists. Please enter a different username.',
            'login_id.unique' => 'This login ID already exists. Please enter a different login ID.',
            'official_email.unique' => 'This official email is already registered. Please enter a different email address.',
        ];
    }
}
