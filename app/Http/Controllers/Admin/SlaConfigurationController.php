<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SlaConfigurationRequest;
use App\Models\SlaConfiguration;
use App\Models\WorkingCalendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SlaConfigurationController extends Controller
{
    public function index(Request $request): View
    {
        $query = SlaConfiguration::query()
            ->with('workingCalendar')
            ->withCount([]);

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('sla_code', 'like', "%{$search}%")
                    ->orWhere('sla_name', 'like', "%{$search}%");

            });
        }

        if ($request->filled('support_level')) {

            $query->where(
                'support_level',
                $request->support_level
            );
        }

        if ($request->filled('status')) {

            $query->where(
                'is_active',
                $request->status === 'active' ? 1 : 0
            );
        }

        $slas = $query
            ->orderBy('sla_name')
            ->paginate(10)
            ->withQueryString();

        $calendars = WorkingCalendar::query()
            ->where('is_active', true)
            ->orderBy('calendar_name')
            ->get([
                'calendar_id',
                'calendar_code',
                'calendar_name',
            ]);

        return view(
            'admin.sla-configurations.index',
            compact(
                'slas',
                'calendars'
            )
        );
    }

    public function create(): View
    {
        $calendars = WorkingCalendar::query()
            ->where('is_active', true)
            ->orderBy('calendar_name')
            ->get([
                'calendar_id',
                'calendar_code',
                'calendar_name',
            ]);

        return view(
            'admin.sla-configurations.create',
            compact('calendars')
        );
    }

    public function store(
        SlaConfigurationRequest $request
    ): RedirectResponse {

        DB::beginTransaction();

        try {

            SlaConfiguration::create([
                'sla_code' => strtoupper(
                    trim($request->sla_code)
                ),

                'sla_name' => trim(
                    $request->sla_name
                ),

                'description' => $request->description
                    ? trim($request->description)
                    : null,

                'response_sla_hours' =>
                    $request->response_sla_hours,

                'resolution_sla_hours' =>
                    $request->resolution_sla_hours,

                'escalation_sla_hours' =>
                    $request->escalation_sla_hours,

                'support_level' =>
                    $request->support_level,

                'calendar_id' =>
                    $request->calendar_id,

                'is_active' =>
                    $request->boolean(
                        'is_active',
                        true
                    ),

                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.sla-configurations.index')
                ->with(
                    'success',
                    'SLA configuration created successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'SLA configuration creation failed.',
                [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create SLA configuration.'
                );
        }
    }

    public function show(
        SlaConfiguration $slaConfiguration
    ): View {

        $slaConfiguration->load(
            'workingCalendar'
        );

        return view(
            'admin.sla-configurations.show',
            compact('slaConfiguration')
        );
    }

    public function edit(
        SlaConfiguration $slaConfiguration
    ): View {

        $calendars = WorkingCalendar::query()
            ->where('is_active', true)
            ->orWhere(
                'calendar_id',
                $slaConfiguration->calendar_id
            )
            ->orderBy('calendar_name')
            ->get([
                'calendar_id',
                'calendar_code',
                'calendar_name',
            ])
            ->unique('calendar_id');

        return view(
            'admin.sla-configurations.edit',
            compact(
                'slaConfiguration',
                'calendars'
            )
        );
    }

    public function update(
        SlaConfigurationRequest $request,
        SlaConfiguration $slaConfiguration
    ): RedirectResponse {

        DB::beginTransaction();

        try {

            $slaConfiguration->update([
                'sla_code' => strtoupper(
                    trim($request->sla_code)
                ),

                'sla_name' => trim(
                    $request->sla_name
                ),

                'description' => $request->description
                    ? trim($request->description)
                    : null,

                'response_sla_hours' =>
                    $request->response_sla_hours,

                'resolution_sla_hours' =>
                    $request->resolution_sla_hours,

                'escalation_sla_hours' =>
                    $request->escalation_sla_hours,

                'support_level' =>
                    $request->support_level,

                'calendar_id' =>
                    $request->calendar_id,

                'is_active' =>
                    $request->boolean('is_active'),

                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.sla-configurations.index')
                ->with(
                    'success',
                    'SLA configuration updated successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'SLA configuration update failed.',
                [
                    'sla_configuration_id' =>
                        $slaConfiguration->sla_configuration_id,

                    'error' => $e->getMessage(),

                    'user_id' => Auth::id(),
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update SLA configuration.'
                );
        }
    }

    public function toggle(
        SlaConfiguration $slaConfiguration
    ): JsonResponse {

        try {

            $slaConfiguration->update([
                'is_active' =>
                    !$slaConfiguration->is_active,

                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,

                'message' =>
                    $slaConfiguration->is_active
                        ? 'SLA activated successfully.'
                        : 'SLA deactivated successfully.',

                'is_active' =>
                    $slaConfiguration->is_active,
            ]);

        } catch (\Throwable $e) {

            Log::error(
                'SLA status change failed.',
                [
                    'sla_configuration_id' =>
                        $slaConfiguration->sla_configuration_id,

                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Unable to change SLA status.',
            ], 500);
        }
    }

    public function destroy(
        SlaConfiguration $slaConfiguration
    ): JsonResponse {

        try {

            /*
             * If later you add:
             *
             * ProjectSupportConfiguration
             * IssueRoutingRule
             * HoIntervention
             *
             * references, check them here before deletion.
             */

            $slaConfiguration->delete();

            return response()->json([
                'success' => true,
                'message' =>
                    'SLA configuration deleted successfully.',
            ]);

        } catch (\Throwable $e) {

            Log::error(
                'SLA deletion failed.',
                [
                    'sla_configuration_id' =>
                        $slaConfiguration->sla_configuration_id,

                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Unable to delete SLA configuration.',
            ], 500);
        }
    }
}