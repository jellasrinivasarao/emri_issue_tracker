<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GroupProjectApplicationMappingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GroupProjectApplicationMappingController extends Controller
{
    public function index(Request $request): View|Response
    {
        $mappings = DB::table('map_group_project_application as m')
            ->join('mst_group as g', 'm.group_id', '=', 'g.group_id')
            ->join('mst_project as p', 'm.project_id', '=', 'p.project_id')
            ->join('mst_application as a', 'm.application_id', '=', 'a.application_id')
            ->select('m.mapping_id', 'm.group_id', 'm.project_id', 'm.application_id', 'g.group_name', 'p.project_name', 'a.application_name', 'm.is_active')
            ->orderBy('g.group_name')->orderBy('p.project_name')->orderBy('a.application_name')->get();

        $format = $request->query('format');
        if ($format) {
            $fileName = 'group-project-application-mapping-' . now()->format('YmdHis') . '.' . $format;
            $rows = $mappings->map(fn ($mapping) => [
                'Group' => $mapping->group_name,
                'Project' => $mapping->project_name,
                'Application' => $mapping->application_name,
                'Status' => (int) $mapping->is_active === 1 ? 'Active' : 'Inactive',
            ])->toArray();

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
                $html = '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;width:100%;"><thead><tr><th>Group</th><th>Project</th><th>Application</th><th>Status</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('group.project.application.mapping')->with('error', 'Unsupported export format.');
        }

        $groups = DB::table('mst_group')->select('group_id', 'group_name')->where('is_active', 1)->orderBy('group_name')->get();
        $projects = DB::table('mst_project')->select('project_id', 'project_name')->where('is_active', 1)->orderBy('project_name')->get();

        return view('pages.group-project-application-mapping', [
            'title' => 'Group Project & Application Mapping',
            'description' => 'Map a group to one project and one or more project applications.',
            'mappings' => $mappings,
            'groups' => $groups,
            'projects' => $projects,
            'permissions' => $this->permissions($request),
        ]);
    }

    public function store(GroupProjectApplicationMappingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $rows = collect($data['application_ids'])->map(fn (int $applicationId) => [
            'group_id' => $data['group_id'], 'project_id' => $data['project_id'], 'application_id' => $applicationId,
            'is_active' => 1, 'created_by' => auth()->id(), 'created_at' => now(),
        ])->all();

        DB::table('map_group_project_application')->insertOrIgnore($rows);

        return redirect()->route('group.project.application.mapping')->with('success', 'Group project application mapping saved successfully.');
    }

    public function applications(int $project_id): JsonResponse
    {
        $applications = DB::table('map_project_application_module as m')
            ->join('mst_application as a', 'm.application_id', '=', 'a.application_id')
            ->join('mst_project as p', 'm.project_id', '=', 'p.project_id')
            ->where(function ($query) use ($project_id) {
                $query->where('m.project_id', $project_id)
                    ->orWhere('p.project_name', (string) $project_id);
            })
            ->where('m.is_active', 1)->where('a.is_active', 1)
            ->select('a.application_id', 'a.application_name')->distinct()->orderBy('a.application_name')->get();

        return response()->json(['applications' => $applications]);
    }

    public function toggle(int $mapping_id): RedirectResponse
    {
        $mapping = DB::table('map_group_project_application')->where('mapping_id', $mapping_id)->first();
        if (! $mapping) {
            return redirect()->route('group.project.application.mapping')->with('error', 'Mapping not found.');
        }

        DB::table('map_group_project_application')->where('mapping_id', $mapping_id)->update([
            'is_active' => ! (int) $mapping->is_active, 'updated_by' => auth()->id(), 'updated_at' => now(),
        ]);

        return redirect()->route('group.project.application.mapping')->with('success', 'Mapping status changed successfully.');
    }

    private function permissions(Request $request): array
    {
        $user = $request->user();
        $route = 'group.project.application.mapping';

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