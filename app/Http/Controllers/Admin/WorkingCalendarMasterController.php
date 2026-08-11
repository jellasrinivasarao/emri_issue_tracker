<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkingCalendarRequest;
use App\Models\WorkingCalendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WorkingCalendarMasterController extends Controller
{
    /**
     * Display calendars.
     */
    public function index(Request $request): View
    {
        $query = WorkingCalendar::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('calendar_code', 'like', "%{$search}%")
                    ->orWhere('calendar_name', 'like', "%{$search}%")
                    ->orWhere('timezone', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'is_active',
                $request->status === 'active' ? 1 : 0
            );
        }

        $calendars = $query
            ->orderBy('calendar_name')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.working-calendars.index',
            compact('calendars')
        );
    }

    /**
     * Store calendar.
     */
    public function store(
        WorkingCalendarRequest $request
    ): RedirectResponse {

        DB::beginTransaction();

        try {

            $calendar = WorkingCalendar::create([
                'calendar_code' => strtoupper(
                    trim($request->calendar_code)
                ),

                'calendar_name' => trim(
                    $request->calendar_name
                ),

                'timezone' => trim(
                    $request->timezone
                ),

                'description' => $request->description
                    ? trim($request->description)
                    : null,

                'is_active' => $request->boolean(
                    'is_active',
                    true
                ),

                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.working-calendars.index')
                ->with(
                    'success',
                    'Working calendar created successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Working calendar creation failed.',
                [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create working calendar.'
                );
        }
    }

    /**
     * Display calendar.
     */
    public function show(
        WorkingCalendar $workingCalendar
    ): View {

        $workingCalendar->load([
            'schedules',
            'holidays',
        ]);

        return view(
            'admin.working-calendars.show',
            compact('workingCalendar')
        );
    }

    /**
     * Update calendar.
     */
    public function update(
        WorkingCalendarRequest $request,
        WorkingCalendar $workingCalendar
    ): RedirectResponse {

        DB::beginTransaction();

        try {

            $workingCalendar->update([
                'calendar_code' => strtoupper(
                    trim($request->calendar_code)
                ),

                'calendar_name' => trim(
                    $request->calendar_name
                ),

                'timezone' => trim(
                    $request->timezone
                ),

                'description' => $request->description
                    ? trim($request->description)
                    : null,

                'is_active' => $request->boolean(
                    'is_active'
                ),

                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.working-calendars.index')
                ->with(
                    'success',
                    'Working calendar updated successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Working calendar update failed.',
                [
                    'working_calendar_id'
                        => $workingCalendar->working_calendar_id,

                    'error' => $e->getMessage(),

                    'user_id' => Auth::id(),
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update working calendar.'
                );
        }
    }

    /**
     * Toggle active/inactive.
     */
    public function toggle(
        WorkingCalendar $workingCalendar
    ): JsonResponse {

        try {

            $workingCalendar->update([
                'is_active' => !$workingCalendar->is_active,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,

                'message' => $workingCalendar->is_active
                    ? 'Working calendar activated successfully.'
                    : 'Working calendar deactivated successfully.',

                'is_active' => $workingCalendar->is_active,
            ]);

        } catch (\Throwable $e) {

            Log::error(
                'Working calendar status change failed.',
                [
                    'working_calendar_id'
                        => $workingCalendar->working_calendar_id,

                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Unable to change calendar status.',
            ], 500);
        }
    }

    /**
     * Delete calendar.
     */
    public function destroy(
        WorkingCalendar $workingCalendar
    ): JsonResponse {

        try {

            /*
            |--------------------------------------------------------------------------
            | Prevent deletion if schedules or holidays exist
            |--------------------------------------------------------------------------
            */

            if ($workingCalendar->schedules()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Calendar cannot be deleted because working schedules exist.',
                ], 422);
            }

            if ($workingCalendar->holidays()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Calendar cannot be deleted because holidays exist.',
                ], 422);
            }

            $workingCalendar->delete();

            return response()->json([
                'success' => true,
                'message' =>
                    'Working calendar deleted successfully.',
            ]);

        } catch (\Throwable $e) {

            Log::error(
                'Working calendar deletion failed.',
                [
                    'working_calendar_id'
                        => $workingCalendar->working_calendar_id,

                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Unable to delete working calendar.',
            ], 500);
        }
    }
}