<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleMasterRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\Response;

class RoleMasterController extends Controller
{
    public function index(Request $request): View|Response
    {
        $roles = Role::query()
            ->select('role_id', 'role_code', 'role_name', 'role_category', 'description', 'is_system_role')
            ->orderBy('role_name')
            ->get();

        $format = $request->query('format');
        if ($format) {
            $fileName = 'role-master-' . now()->format('YmdHis') . '.' . $format;
            $rows = $roles->map(function (Role $role) {
                return $this->formatRoleRow($role);
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
                $html .= '<thead><tr><th>Role Code</th><th>Role Name</th><th>Category</th><th>Description</th><th>System Role</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('role.master')->with('error', 'Unsupported export format.');
        }

        return view('pages.role-master', [
            'title' => 'Role Master',
            'description' => 'Manage roles, role categories, and role definitions.',
            'roles' => $roles,
        ]);
    }

    public function store(RoleMasterRequest $request): RedirectResponse
    {
        $role = new Role();
        $role->role_code = $this->generateRoleCode();
        $role->role_name = $request->role_name;
        $role->role_category = $request->role_category;
        $role->description = $request->description;

        if (Schema::hasColumn('mst_role', 'created_at')) {
            $role->created_at = now();
        }

        if (Schema::hasColumn('mst_role', 'created_by')) {
            $role->created_by = auth()->id();
        }

        $role->save();

        return redirect()->route('role.master')->with('success', 'Role created successfully.');
    }

    public function update(RoleMasterRequest $request, int $role_id): RedirectResponse
    {
        $role = Role::findOrFail($role_id);
        $role->role_name = $request->role_name;
        $role->role_category = $request->role_category;
        $role->description = $request->description;

        if (Schema::hasColumn('mst_role', 'update_at')) {
            $role->update_at = now();
        }
        if (Schema::hasColumn('mst_role', 'updated_at')) {
            $role->updated_at = now();
        }

        if (Schema::hasColumn('mst_role', 'updated_by')) {
            $role->updated_by = auth()->id();
        }
        if (Schema::hasColumn('mst_role', 'update_by')) {
            $role->update_by = auth()->id();
        }

        $role->save();

        return redirect()->route('role.master')->with('success', 'Role updated successfully.');
    }

    public function toggle(Request $request, int $role_id): RedirectResponse
    {
        $role = Role::findOrFail($role_id);
        $role->is_system_role = ! $role->is_system_role;

        if (Schema::hasColumn('mst_role', 'update_at')) {
            $role->update_at = now();
        }
        if (Schema::hasColumn('mst_role', 'updated_at')) {
            $role->updated_at = now();
        }

        if (Schema::hasColumn('mst_role', 'updated_by')) {
            $role->updated_by = auth()->id();
        }
        if (Schema::hasColumn('mst_role', 'update_by')) {
            $role->update_by = auth()->id();
        }

        $role->save();

        return redirect()->route('role.master')->with('success', $role->is_system_role ? 'Role enabled successfully.' : 'Role disabled successfully.');
    }

    private function formatRoleRow(Role $role): array
    {
        return [
            'Role Code' => $role->role_code,
            'Role Name' => $role->role_name,
            'Category' => $role->role_category ?? '-',
            'Description' => $role->description ?? '-',
            'System Role' => $role->is_system_role ? 'Yes' : 'No',
        ];
    }

    private function generateRoleCode(): string
    {
        $last = Role::orderByDesc('role_id')->limit(1)->first();
        $next = $last ? ((int) $last->role_id + 1) : 1;
        return 'RL' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
