<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserMasterRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\State;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\Response;

class UserMasterController extends Controller
{
    public function index(Request $request): View|Response
    {
        $users = User::query()
            ->with('roles')
            ->select(
                'user_id',
                'employee_code',
                'user_name',
                'login_id',
                'official_email',
                'mobile_number',
                'user_status',
                'role_id',
                'is_active'
            )
            ->orderBy('user_name')
            ->get();

        $currentRoleId = auth()->user()->role_id ?? null;
        if ($currentRoleId) {
            $mapped = DB::table('map_role_hierarchy')->where('parent_role_id', $currentRoleId)->pluck('child_role_id')->toArray();
            $mapped[] = $currentRoleId;
            $roles = Role::query()
                ->select('role_id', 'role_name')
                ->whereIn('role_id', array_unique($mapped))
                ->orderBy('role_name')
                ->get();
        } else {
            $roles = Role::query()
                ->select('role_id', 'role_name')
                ->orderBy('role_name')
                ->get();
        }

        $format = $request->query('format');
        if ($format) {
            $fileName = 'user-master-' . now()->format('YmdHis') . '.' . $format;
            $rows = $users->map(function (User $user) {
                return [
                    'Employee Code' => $user->employee_code,
                    'User Name' => $user->user_name,
                    'Login ID' => $user->login_id,
                    'Email' => $user->official_email,
                    'Mobile' => $user->mobile_number ?? '-',
                    'Role' => $user->roles->pluck('role_name')->join(', ') ?: '-',
                    'Status' => $user->is_active ? 'Active' : 'Inactive',
                ];
            })->toArray();

            if (in_array($format, ['csv', 'xlsx'], true)) {
                $output = '';
                $output .= implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', $value) . '"', array_keys($rows[0] ?? []))) . "\r\n";
                foreach ($rows as $row) {
                    $output .= implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', $value) . '"', $row)) . "\r\n";
                }

                return response($output, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            if ($format === 'pdf') {
                $html = '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;width:100%;">';
                $html .= '<thead><tr><th>Employee Code</th><th>User Name</th><th>Login ID</th><th>Email</th><th>Mobile</th><th>Role</th><th>Status</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('user.master')->with('error', 'Unsupported export format.');
        }

        $states = State::query()->select('state_id', 'state_name')->orderBy('state_name')->get();
        $vendors = Vendor::query()->select('vendor_id', 'vendor_name')->orderBy('vendor_name')->get();

        return view('pages.user-master', [
            'title' => 'User Master',
            'description' => 'Manage users, login details, and role assignments.',
            'users' => $users,
            'roles' => $roles,
            'states' => $states,
            'vendors' => $vendors,
        ]);
    }

    public function store(UserMasterRequest $request): RedirectResponse
    {
        $user = new User();
        // generate employee code if not provided
        if (empty($request->employee_code)) {
            $next = (int) User::max('user_id') + 1;
            $employeeCode = 'EMP' . str_pad($next, 4, '0', STR_PAD_LEFT);
            while (User::where('employee_code', $employeeCode)->exists()) {
                $next++;
                $employeeCode = 'EMP' . str_pad($next, 4, '0', STR_PAD_LEFT);
            }
            $user->employee_code = $employeeCode;
        } else {
            $user->employee_code = $request->employee_code;
        }
        $user->user_name = $request->user_name;
        // if login_id not provided, derive from username and ensure uniqueness
        if (empty($request->login_id)) {
            $base = Str::of($request->user_name)->lower()->slug('_')->__toString();
            $login = $base;
            $i = 1;
            while (User::where('login_id', $login)->exists()) {
                $login = $base . '_' . $i++;
            }
            $user->login_id = $login;
        } else {
            $user->login_id = $request->login_id;
        }
        $user->official_email = $request->official_email;
        $user->mobile_number = $request->mobile_number;
        $user->user_status = $request->user_status ?? 'Active';
        $user->role_id = $request->role_id;
        $user->is_active = 1;

        if ($request->filled('password')) {
            $user->password_hash = Hash::make($request->password);
        }

        // Persist state(s) and vendor if columns exist
        if (Schema::hasColumn('mst_user', 'state_id')) {
            $stateIds = is_array($request->state_ids) ? array_values(array_filter($request->state_ids, fn($value) => $value !== null && $value !== '')) : [];
            $columnType = Schema::getColumnType('mst_user', 'state_id');

            if (! empty($stateIds)) {
                if (in_array($columnType, ['string', 'text', 'json'], true)) {
                    $user->state_id = implode(',', $stateIds);
                } else {
                    $user->state_id = $stateIds[0];
                }
            } elseif ($request->filled('state_id')) {
                $user->state_id = $request->state_id;
            }
        }

        if (Schema::hasColumn('mst_user', 'vendor_id') && $request->filled('vendor_id')) {
            $user->vendor_id = $request->vendor_id;
        }

        // If current user is a State Admin, ensure they cannot assign outside their mapped state(s)
        if (auth()->user()?->hasRole('State Admin') && Schema::hasColumn('mst_user', 'state_id')) {
            $currentState = auth()->user()->state_id ?? null;
            if ($currentState && $user->state_id) {
                $assigned = (string) $user->state_id;
                if (! str_contains($assigned, (string) $currentState)) {
                    return redirect()->route('user.master')->with('error', 'You may only assign users to states you manage.');
                }
            }
        }

        if (Schema::hasColumn('mst_user', 'created_at')) {
            $user->created_at = now();
        }

        if (Schema::hasColumn('mst_user', 'created_by')) {
            $user->created_by = auth()->id();
        }

        DB::transaction(function () use ($user, $request) {
            $user->save();
            if ($request->filled('role_id')) {
                $user->roles()->sync([$request->role_id]);
            }
        });

        return redirect()->route('user.master')->with('success', 'User created successfully.');
    }

    public function update(UserMasterRequest $request, int $user_id): RedirectResponse
    {
        $user = User::findOrFail($user_id);
        $user->employee_code = $request->employee_code;
        $user->user_name = $request->user_name;
        $user->login_id = $request->login_id;
        $user->official_email = $request->official_email;
        $user->mobile_number = $request->mobile_number;
        if ($request->filled('user_status')) {
            $user->user_status = $request->user_status;
        }
        $user->role_id = $request->role_id;

        if ($request->filled('password')) {
            $user->password_hash = Hash::make($request->password);
        }

        // Persist state(s) and vendor on update
        if (Schema::hasColumn('mst_user', 'state_id')) {
            $stateIds = is_array($request->state_ids) ? array_values(array_filter($request->state_ids, fn($value) => $value !== null && $value !== '')) : [];
            $columnType = Schema::getColumnType('mst_user', 'state_id');

            if (! empty($stateIds)) {
                if (in_array($columnType, ['string', 'text', 'json'], true)) {
                    $user->state_id = implode(',', $stateIds);
                } else {
                    $user->state_id = $stateIds[0];
                }
            } elseif ($request->filled('state_id')) {
                $user->state_id = $request->state_id;
            }
        }

        if (Schema::hasColumn('mst_user', 'vendor_id') && $request->filled('vendor_id')) {
            $user->vendor_id = $request->vendor_id;
        }

        if (auth()->user()?->hasRole('State Admin') && Schema::hasColumn('mst_user', 'state_id')) {
            $currentState = auth()->user()->state_id ?? null;
            if ($currentState && $user->state_id) {
                $assigned = (string) $user->state_id;
                if (! str_contains($assigned, (string) $currentState)) {
                    return redirect()->route('user.master')->with('error', 'You may only assign users to states you manage.');
                }
            }
        }

        if (Schema::hasColumn('mst_user', 'updated_at')) {
            $user->updated_at = now();
        }
        if (Schema::hasColumn('mst_user', 'updated_by')) {
            $user->updated_by = auth()->id();
        }

        DB::transaction(function () use ($user, $request) {
            $user->save();
            if ($request->filled('role_id')) {
                $user->roles()->sync([$request->role_id]);
            }
        });

        return redirect()->route('user.master')->with('success', 'User updated successfully.');
    }

    public function toggle(Request $request, int $user_id): RedirectResponse
    {
        $user = User::findOrFail($user_id);
        $user->is_active = ! $user->is_active;

        if (Schema::hasColumn('mst_user', 'updated_at')) {
            $user->updated_at = now();
        }
        if (Schema::hasColumn('mst_user', 'updated_by')) {
            $user->updated_by = auth()->id();
        }

        $user->save();

        return redirect()->route('user.master')->with('success', $user->is_active ? 'User activated successfully.' : 'User deactivated successfully.');
    }
}
