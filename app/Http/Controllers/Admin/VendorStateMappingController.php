<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VendorStateMappingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorStateMappingController extends Controller
{
    public function index(Request $request): View|Response
    {
        $format = $request->query('format');

        $mappings = DB::table('map_vendor_state as m')
            ->join('mst_vendor as v', 'm.vendor_id', '=', 'v.vendor_id')
            ->join('mst_state as s', 'm.state_id', '=', 's.state_id')
            ->join('mst_project as p', 'm.project_id', '=', 'p.project_id')
            ->select(
                'm.mapping_id',
                'm.vendor_id',
                'm.state_id',
                'm.project_id',
                'v.vendor_name',
                's.state_name',
                'p.project_name',
                'm.is_active',
                'm.created_by',
                'm.created_at',
                'm.updated_by',
                'm.update_at'
            )
            ->orderBy('v.vendor_name')
            ->orderBy('s.state_name')
            ->orderBy('p.project_name')
            ->get();

        $vendors = DB::table('mst_vendor')
            ->select('vendor_id', 'vendor_name')
            ->where('is_active', 1)
            ->orderBy('vendor_name')
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

        if ($format) {
            $fileName = 'vendor-state-mapping-' . now()->format('YmdHis') . '.' . $format;
            $rows = $mappings->map(function ($mapping) {
                return [
                    'ID' => $mapping->mapping_id,
                    'Vendor' => $mapping->vendor_name,
                    'State' => $mapping->state_name,
                    'Project' => $mapping->project_name,
                    'Status' => $mapping->is_active ? 'Active' : 'Inactive',
                ];
            })->toArray();

            if (in_array($format, ['csv', 'xlsx'], true)) {
                $output = '';
                foreach (array_keys($rows[0] ?? []) as $header) {
                    $output .= $header . ',';
                }
                $output = rtrim($output, ',') . "\n";

                foreach ($rows as $row) {
                    $output .= implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', $value) . '"', $row)) . "\n";
                }

                return response($output, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('vendor.state.mapping')->with('error', 'Unsupported export format.');
        }

        return view('pages.vendor-state-mapping', [
            'title' => 'Vendor State Mapping',
            'description' => 'Map states and projects to vendors and manage active vendor mappings.',
            'mappings' => $mappings,
            'vendors' => $vendors,
            'states' => $states,
            'projects' => $projects,
        ]);
    }

    public function store(VendorStateMappingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $vendorId = $data['vendor_id'];
        $stateIds = $data['state_ids'];
        $projectIds = $data['project_ids'];
        $timestamp = now();
        $userId = auth()->id();

        $insertData = [];
        foreach ($stateIds as $stateId) {
            foreach ($projectIds as $projectId) {
                $insertData[] = [
                    'vendor_id' => $vendorId,
                    'state_id' => $stateId,
                    'project_id' => $projectId,
                    'is_active' => 1,
                    'created_by' => $userId,
                    'created_at' => $timestamp,
                ];
            }
        }

        if (! empty($insertData)) {
            DB::table('map_vendor_state')->insertOrIgnore($insertData);
        }

        return redirect()->route('vendor.state.mapping')->with('success', 'Vendor state mapping added successfully.');
    }

    public function update(VendorStateMappingRequest $request, int $mapping_id): RedirectResponse
    {
        $data = $request->validated();
        $vendorId = $data['vendor_id'];
        $stateIds = $data['state_ids'];
        $projectIds = $data['project_ids'];
        $timestamp = now();
        $userId = auth()->id();

        $existing = DB::table('map_vendor_state')->where('mapping_id', $mapping_id)->first();
        if (! $existing) {
            return redirect()->route('vendor.state.mapping')->with('error', 'Mapping not found.');
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

        $duplicateExists = DB::table('map_vendor_state')
            ->where('vendor_id', $vendorId)
            ->where('state_id', $firstCombination['state_id'])
            ->where('project_id', $firstCombination['project_id'])
            ->where('mapping_id', '<>', $mapping_id)
            ->exists();

        if ($duplicateExists) {
            return redirect()->route('vendor.state.mapping')->with('error', 'This mapping already exists.');
        }

        DB::table('map_vendor_state')
            ->where('mapping_id', $mapping_id)
            ->update([
                'vendor_id' => $vendorId,
                'state_id' => $firstCombination['state_id'],
                'project_id' => $firstCombination['project_id'],
                'updated_by' => $userId,
                'update_at' => $timestamp,
            ]);

        $insertData = [];
        foreach ($combinations as $combination) {
            $insertData[] = [
                'vendor_id' => $vendorId,
                'state_id' => $combination['state_id'],
                'project_id' => $combination['project_id'],
                'is_active' => 1,
                'created_by' => $userId,
                'created_at' => $timestamp,
            ];
        }

        if (! empty($insertData)) {
            DB::table('map_vendor_state')->insertOrIgnore($insertData);
        }

        return redirect()->route('vendor.state.mapping')->with('success', 'Vendor state mapping updated successfully.');
    }

    public function options(): JsonResponse
    {
        $vendors = DB::table('mst_vendor')
            ->select('vendor_id', 'vendor_name')
            ->where('is_active', 1)
            ->orderBy('vendor_name')
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

        return response()->json([
            'vendors' => $vendors,
            'states' => $states,
            'projects' => $projects,
        ]);
    }

    public function toggle(int $mapping_id): RedirectResponse
    {
        $mapping = DB::table('map_vendor_state')->where('mapping_id', $mapping_id)->first();
        if (! $mapping) {
            return redirect()->route('vendor.state.mapping')->with('error', 'Mapping not found.');
        }

        DB::table('map_vendor_state')
            ->where('mapping_id', $mapping_id)
            ->update([
                'is_active' => ! (int) $mapping->is_active,
                'updated_by' => auth()->id(),
                'update_at' => now(),
            ]);

        return redirect()->route('vendor.state.mapping')->with('success', 'Vendor state mapping status changed successfully.');
    }
}
