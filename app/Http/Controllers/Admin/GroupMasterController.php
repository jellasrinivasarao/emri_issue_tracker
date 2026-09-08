<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GroupMasterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\View\View;

class GroupMasterController extends Controller
{
    public function index(Request $request): View|Response
    {
        $groups = DB::table('mst_group as g')
            ->leftJoin('users as creator', 'g.created_by', '=', 'creator.id')
            ->leftJoin('users as updater', 'g.updated_by', '=', 'updater.id')
            ->select('g.*', 'creator.name as created_by_name', 'updater.name as updated_by_name')
            ->orderBy('g.group_name')
            ->get();

        $format = $request->query('format');
        if ($format) {
            $fileName = 'group-master-' . now()->format('YmdHis') . '.' . $format;
            $rows = $groups->map(function ($group) {
                return [
                    'Group Name' => $group->group_name,
                    'Description' => $group->description ?: '-',
                    'Active' => (int) $group->is_active === 1 ? 'Active' : 'Inactive',
                    'Created By' => $group->created_by_name ?: $group->created_by ?: '-',
                    'Created At' => $group->created_at ?: '-',
                    'Updated By' => $group->updated_by_name ?: $group->updated_by ?: '-',
                    'Updated At' => $group->updated_at ?: '-',
                ];
            })->toArray();

            if (in_array($format, ['csv', 'xlsx'], true)) {
                $output = implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', $value) . '"', array_keys($rows[0] ?? []))) . "\r\n";
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
                $html .= '<thead><tr><th>Group Name</th><th>Description</th><th>Active</th><th>Created By</th><th>Created At</th><th>Updated By</th><th>Updated At</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('group.master')->with('error', 'Unsupported export format.');
        }

        $permissions = $this->permissions($request);

        return view('pages.group-master', [
            'title' => 'Group Master',
            'description' => 'Manage groups used for project and application access.',
            'groups' => $groups,
            'permissions' => $permissions,
        ]);
    }

    public function store(GroupMasterRequest $request): RedirectResponse
    {
        DB::table('mst_group')->insert([
            'group_name' => $request->group_name,
            'description' => $request->description,
            'is_active' => 1,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        return redirect()->route('group.master')->with('success', 'Group created successfully.');
    }

    public function update(GroupMasterRequest $request, int $group_id): RedirectResponse
    {
        DB::table('mst_group')->where('group_id', $group_id)->update([
            'group_name' => $request->group_name,
            'description' => $request->description,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        return redirect()->route('group.master')->with('success', 'Group updated successfully.');
    }

    public function toggle(int $group_id): RedirectResponse
    {
        $group = DB::table('mst_group')->where('group_id', $group_id)->first();
        if (! $group) {
            return redirect()->route('group.master')->with('error', 'Group not found.');
        }

        $newStatus = (int) $group->is_active === 1 ? 0 : 1;
        DB::table('mst_group')->where('group_id', $group_id)->update([
            'is_active' => $newStatus,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        return redirect()->route('group.master')->with('success', $newStatus === 1 ? 'Group reactivated successfully.' : 'Group disabled successfully.');
    }

    private function permissions(Request $request): array
    {
        $user = $request->user();
        $route = 'group.master';

        return [
            'view' => $user->hasPrivilegeOnRoute($route, 'view'),
            'create' => $user->hasPrivilegeOnRoute($route, 'create'),
            'edit' => $user->hasPrivilegeOnRoute($route, 'edit'),
            'export' => $user->hasPrivilegeOnRoute($route, 'export'),
            'activate' => $user->hasPrivilegeOnRoute($route, 'activate'),
            'deactivate' => $user->hasPrivilegeOnRoute($route, 'deactivate'),
        ];
    }
}