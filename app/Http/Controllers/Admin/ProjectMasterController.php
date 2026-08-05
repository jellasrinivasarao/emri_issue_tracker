<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectMasterRequest;
use App\Models\Project;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\Response;

class ProjectMasterController extends Controller
{
    public function index(Request $request): View|Response
    {
        $projects = Project::query()
            ->select('project_id', 'project_name', 'short_code', 'project_code', 'project_description', 'is_active')
            ->orderBy('project_id')
            ->get();

        $format = $request->query('format');
        if ($format) {
            $fileName = 'project-master-' . now()->format('YmdHis') . '.' . $format;
            $rows = $projects->map(function (Project $project) {
                return [
                    'Project ID' => $project->project_id,
                    'Project Name' => $project->project_name,
                    'Short Code' => $project->short_code ?? '-',
                    'Description' => $project->project_description ?? '-',
                    'Status' => $project->is_active ? 'Active' : 'Inactive',
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
                $html .= '<thead><tr><th>Project ID</th><th>Project Name</th><th>Short Code</th><th>Description</th><th>Status</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('project.master')->with('error', 'Unsupported export format.');
        }

        return view('pages.project-master', [
            'title' => 'Project Master',
            'description' => 'Manage projects, customers, and project master details.',
            'projects' => $projects,
        ]);
    }

    public function store(ProjectMasterRequest $request): RedirectResponse
    {
        $project = new Project();
        $project->project_name = $request->project_name;
        $project->project_code = $this->generateProjectCode();
        $project->short_code = $request->short_code;
        $project->project_description = $request->project_description;
        $project->is_active = 1;
        $project->state_id = $this->resolveStateId($request);

        if (Schema::hasColumn('mst_project', 'created_at')) {
            $project->created_at = now();
        }

        if (Schema::hasColumn('mst_project', 'created_by')) {
            $project->created_by = auth()->id();
        }

        $project->save();

        return redirect()->route('project.master')->with('success', 'Project created successfully.');
    }

    public function update(ProjectMasterRequest $request, int $project_id): RedirectResponse
    {
        $project = Project::findOrFail($project_id);
        $project->project_name = $request->project_name;
        $project->short_code = $request->short_code;
        $project->project_description = $request->project_description;
        $project->state_id = $this->resolveStateId($request, $project->state_id);

        if (Schema::hasColumn('mst_project', 'update_at')) {
            $project->update_at = now();
        }
        if (Schema::hasColumn('mst_project', 'updated_at')) {
            $project->updated_at = now();
        }

        if (Schema::hasColumn('mst_project', 'updated_by')) {
            $project->updated_by = auth()->id();
        }
        if (Schema::hasColumn('mst_project', 'update_by')) {
            $project->update_by = auth()->id();
        }

        $project->save();

        return redirect()->route('project.master')->with('success', 'Project updated successfully.');
    }

    private function generateProjectCode(): string
    {
        $nextId = (int) Project::query()->max('project_id') + 1;
        return 'PRJ' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    private function resolveStateId(Request $request, ?int $fallback = null): int
    {
        if ($request->filled('state_id')) {
            return (int) $request->state_id;
        }

        if ($fallback !== null) {
            return $fallback;
        }

        $state = State::query()->orderBy('state_id')->first();

        return $state?->state_id ?? 1;
    }

    public function toggle(Request $request, int $project_id): RedirectResponse
    {
        $project = Project::findOrFail($project_id);
        $project->is_active = ! $project->is_active;

        if (Schema::hasColumn('mst_project', 'update_at')) {
            $project->update_at = now();
        }
        if (Schema::hasColumn('mst_project', 'updated_at')) {
            $project->updated_at = now();
        }

        if (Schema::hasColumn('mst_project', 'updated_by')) {
            $project->updated_by = auth()->id();
        }
        if (Schema::hasColumn('mst_project', 'update_by')) {
            $project->update_by = auth()->id();
        }

        $project->save();

        return redirect()->route('project.master')->with('success', $project->is_active ? 'Project reactivated successfully.' : 'Project disabled successfully.');
    }
}
