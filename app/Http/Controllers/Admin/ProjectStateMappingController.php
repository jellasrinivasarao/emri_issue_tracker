<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectStateMappingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectStateMappingController extends Controller
{
    public function index(): View
    {
        $mappings = DB::table('map_project_state as m')
            ->join('mst_state as s', 'm.state_id', '=', 's.state_id')
            ->join('mst_project as p', 'm.project_id', '=', 'p.project_id')
            ->select(
                'm.mapping_id',
                'm.state_id',
                'm.project_id',
                's.state_name',
                'p.project_name',
                'm.is_active',
                'm.created_by',
                'm.created_at',
                'm.updated_by',
                'm.update_at'
            )
            ->orderBy('s.state_name')
            ->orderBy('p.project_name')
            ->get();

        $states = DB::table('mst_state')
            ->select('state_id', 'state_name')
            ->where('is_active', 1)
            ->orderBy('state_name')
            ->get();

        $projects = DB::table('mst_project')
            ->select('project_id', 'project_name')
            ->where('is_active', 1)
            ->orderBy('project_name')
            ->get();

        return view('pages.project-state-mapping', [
            'title' => 'Project State Mapping',
            'description' => 'Map states to projects and manage active state-project mappings.',
            'mappings' => $mappings,
            'states' => $states,
            'projects' => $projects,
        ]);
    }

    public function store(ProjectStateMappingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $stateIds = $data['state_ids'];
        $projectIds = $data['project_ids'];
        $timestamp = now();
        $userId = auth()->id();

        $insertData = [];
        foreach ($stateIds as $stateId) {
            foreach ($projectIds as $projectId) {
                $insertData[] = [
                    'state_id' => $stateId,
                    'project_id' => $projectId,
                    'is_active' => 1,
                    'created_by' => $userId,
                    'created_at' => $timestamp,
                ];
            }
        }

        if (! empty($insertData)) {
            DB::table('map_project_state')->insertOrIgnore($insertData);
        }

        return redirect()->route('project.state.mapping')->with('success', 'Project state mapping added successfully.');
    }

    public function update(ProjectStateMappingRequest $request, int $mapping_id): RedirectResponse
    {
        $data = $request->validated();
        $stateIds = $data['state_ids'];
        $projectIds = $data['project_ids'];
        $timestamp = now();
        $userId = auth()->id();

        $existing = DB::table('map_project_state')->where('mapping_id', $mapping_id)->first();
        if (! $existing) {
            return redirect()->route('project.state.mapping')->with('error', 'Mapping not found.');
        }

        $combinations = [];
        foreach ($stateIds as $stateId) {
            foreach ($projectIds as $projectId) {
                $combinations[] = [
                    'state_id' => $stateId,
                    'project_id' => $projectId,
                ];
            }
        }

        $firstCombination = array_shift($combinations);

        $duplicateExists = DB::table('map_project_state')
            ->where('state_id', $firstCombination['state_id'])
            ->where('project_id', $firstCombination['project_id'])
            ->where('mapping_id', '<>', $mapping_id)
            ->exists();

        if ($duplicateExists) {
            return redirect()->route('project.state.mapping')->with('error', 'This mapping already exists.');
        }

        DB::table('map_project_state')
            ->where('mapping_id', $mapping_id)
            ->update([
                'state_id' => $firstCombination['state_id'],
                'project_id' => $firstCombination['project_id'],
                'updated_by' => $userId,
                'update_at' => $timestamp,
            ]);

        $insertData = [];
        foreach ($combinations as $combination) {
            $insertData[] = [
                'state_id' => $combination['state_id'],
                'project_id' => $combination['project_id'],
                'is_active' => 1,
                'created_by' => $userId,
                'created_at' => $timestamp,
            ];
        }

        if (! empty($insertData)) {
            DB::table('map_project_state')->insertOrIgnore($insertData);
        }

        return redirect()->route('project.state.mapping')->with('success', 'Project state mapping updated successfully.');
    }

    public function options(): JsonResponse
    {
        $states = DB::table('mst_state')
            ->select('state_id', 'state_name')
            ->where('is_active', 1)
            ->orderBy('state_name')
            ->get();

        $projects = DB::table('mst_project')
            ->select('project_id', 'project_name')
            ->where('is_active', 1)
            ->orderBy('project_name')
            ->get();

        return response()->json([
            'states' => $states,
            'projects' => $projects,
        ]);
    }

    public function toggle(int $mapping_id): RedirectResponse
    {
        $mapping = DB::table('map_project_state')->where('mapping_id', $mapping_id)->first();
        if (! $mapping) {
            return redirect()->route('project.state.mapping')->with('error', 'Mapping not found.');
        }

        DB::table('map_project_state')
            ->where('mapping_id', $mapping_id)
            ->update([
                'is_active' => ! (int) $mapping->is_active,
                'updated_by' => auth()->id(),
                'update_at' => now(),
            ]);

        return redirect()->route('project.state.mapping')->with('success', 'Project state mapping status changed successfully.');
    }
}
