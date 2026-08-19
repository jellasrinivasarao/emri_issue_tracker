<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectApplicationModuleMappingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectApplicationModuleMappingController extends Controller
{
    public function index(): View
    {
        $mappings = DB::table('map_project_application_module as m')
            ->join('mst_project as p', 'm.project_id', '=', 'p.project_id')
            ->join('mst_application as a', 'm.application_id', '=', 'a.application_id')
            ->join('mst_module as mod', 'm.module_id', '=', 'mod.module_id')
            ->select(
                'm.mapping_id',
                'm.project_id',
                'm.application_id',
                'm.module_id',
                'p.project_name',
                'a.application_name',
                'mod.module_name',
                'm.is_active',
                'm.created_by',
                'm.created_at',
                'm.updated_by',
                'm.update_at'
            )
            ->orderBy('m.mapping_id')
            ->get();

        $projects = DB::table('mst_project')
            ->select('project_id', 'project_name')
            ->where('is_active', 1)
            ->orderBy('project_name')
            ->get();

        $applications = DB::table('mst_application')
            ->select('application_id', 'application_name')
            ->where('is_active', 1)
            ->orderBy('application_name')
            ->get();

        $modules = DB::table('mst_module')
            ->select('module_id', 'module_name')
            ->where('is_active', 1)
            ->orderBy('module_name')
            ->get();

        return view('pages.project-application-module-mapping', [
            'title' => 'Project Mappings',
            'description' => 'Map projects to applications and modules and manage active mappings.',
            'mappings' => $mappings,
            'projects' => $projects,
            'applications' => $applications,
            'modules' => $modules,
        ]);
    }

    public function store(ProjectApplicationModuleMappingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $projectId = $data['project_id'];
        $applicationIds = $data['application_ids'];
        $moduleIds = $data['module_ids'];
        $timestamp = now();
        $userId = auth()->id();

        $insertData = [];
        foreach ($applicationIds as $applicationId) {
            foreach ($moduleIds as $moduleId) {
                $insertData[] = [
                    'project_id' => $projectId,
                    'application_id' => $applicationId,
                    'module_id' => $moduleId,
                    'is_active' => 1,
                    'created_by' => $userId,
                    'created_at' => $timestamp,
                ];
            }
        }

        if (! empty($insertData)) {
            DB::table('map_project_application_module')->insertOrIgnore($insertData);
        }

        return redirect()->route('project.application.module.mapping')->with('success', 'Project application module mapping added successfully.');
    }

    public function update(ProjectApplicationModuleMappingRequest $request, int $mapping_id): RedirectResponse
    {
        $data = $request->validated();
        $projectId = $data['project_id'];
        $applicationIds = $data['application_ids'];
        $moduleIds = $data['module_ids'];
        $timestamp = now();
        $userId = auth()->id();

        $existing = DB::table('map_project_application_module')->where('mapping_id', $mapping_id)->first();
        if (! $existing) {
            return redirect()->route('project.application.module.mapping')->with('error', 'Mapping not found.');
        }

        $combinations = [];
        foreach ($applicationIds as $applicationId) {
            foreach ($moduleIds as $moduleId) {
                $combinations[] = [
                    'project_id' => $projectId,
                    'application_id' => $applicationId,
                    'module_id' => $moduleId,
                ];
            }
        }

        $firstCombination = array_shift($combinations);

        $duplicateExists = DB::table('map_project_application_module')
            ->where('project_id', $firstCombination['project_id'])
            ->where('application_id', $firstCombination['application_id'])
            ->where('module_id', $firstCombination['module_id'])
            ->where('mapping_id', '<>', $mapping_id)
            ->exists();

        if ($duplicateExists) {
            return redirect()->route('project.application.module.mapping')->with('error', 'This mapping already exists.');
        }

        DB::table('map_project_application_module')
            ->where('mapping_id', $mapping_id)
            ->update([
                'project_id' => $firstCombination['project_id'],
                'application_id' => $firstCombination['application_id'],
                'module_id' => $firstCombination['module_id'],
                'updated_by' => $userId,
                'update_at' => $timestamp,
            ]);

        $insertData = [];
        foreach ($combinations as $combination) {
            $insertData[] = [
                'project_id' => $combination['project_id'],
                'application_id' => $combination['application_id'],
                'module_id' => $combination['module_id'],
                'is_active' => 1,
                'created_by' => $userId,
                'created_at' => $timestamp,
            ];
        }

        if (! empty($insertData)) {
            DB::table('map_project_application_module')->insertOrIgnore($insertData);
        }

        return redirect()->route('project.application.module.mapping')->with('success', 'Project application module mapping updated successfully.');
    }

    public function options(): JsonResponse
    {
        $applications = DB::table('mst_application')
            ->select('application_id', 'application_name')
            ->where('is_active', 1)
            ->orderBy('application_name')
            ->get();

        $modules = DB::table('mst_module')
            ->select('module_id', 'module_name')
            ->where('is_active', 1)
            ->orderBy('module_name')
            ->get();

        return response()->json([
            'applications' => $applications,
            'modules' => $modules,
        ]);
    }

    public function toggle(int $mapping_id): RedirectResponse
    {
        $mapping = DB::table('map_project_application_module')->where('mapping_id', $mapping_id)->first();
        if (! $mapping) {
            return redirect()->route('project.application.module.mapping')->with('error', 'Mapping not found.');
        }

        DB::table('map_project_application_module')
            ->where('mapping_id', $mapping_id)
            ->update([
                'is_active' => ! (int) $mapping->is_active,
                'updated_by' => auth()->id(),
                'update_at' => now(),
            ]);

        return redirect()->route('project.application.module.mapping')->with('success', 'Project application module mapping status changed successfully.');
    }
}
