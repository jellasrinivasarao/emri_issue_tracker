<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Http\Requests\WorkingScheduleRequest;
use App\Models\WorkingSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WorkingHoursController extends Controller
{
    /**
     * Display working schedules.
     */
    public function index(Request $request): View
    {
        $query = WorkingSchedule::query();

        if ($request->filled('calendar_id')) {
            $query->where('calendar_id', $request->calendar_id);
        }

        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $schedules = $query
            ->orderBy('calendar_id')
            ->orderByRaw("
                CASE day_of_week
                    WHEN 'MONDAY' THEN 1
                    WHEN 'TUESDAY' THEN 2
                    WHEN 'WEDNESDAY' THEN 3
                    WHEN 'THURSDAY' THEN 4
                    WHEN 'FRIDAY' THEN 5
                    WHEN 'SATURDAY' THEN 6
                    WHEN 'SUNDAY' THEN 7
                    ELSE 8
                END
            ")
            ->orderBy('shift_no')
            ->orderBy('sequence_no')
            ->paginate(20)
            ->withQueryString();

        return view(
            'working-schedules.index',
            compact('schedules')
        );
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('working-schedules.create');
    }

    /**
     * Store working schedule.
     */
    public function store(
        WorkingScheduleRequest $request
    ): RedirectResponse {

        $data = $request->validated();

        $data['created_by'] = Auth::id();

        WorkingSchedule::create($data);

        return redirect()
            ->route('working-schedules.index')
            ->with('success', 'Working schedule created successfully.');
    }

    /**
     * Show single schedule.
     */
    public function show(
        WorkingSchedule $workingSchedule
    ): View {

        return view(
            'working-schedules.show',
            compact('workingSchedule')
        );
    }

    /**
     * Show edit form.
     */
    public function edit(
        WorkingSchedule $workingSchedule
    ): View {

        return view(
            'working-schedules.edit',
            compact('workingSchedule')
        );
    }

    /**
     * Update schedule.
     */
    public function update(
        WorkingScheduleRequest $request,
        WorkingSchedule $workingSchedule
    ): RedirectResponse {

        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $workingSchedule->update($data);

        return redirect()
            ->route('working-schedules.index')
            ->with('success', 'Working schedule updated successfully.');
    }

    /**
     * Delete schedule.
     */
    public function destroy(
        WorkingSchedule $workingSchedule
    ): RedirectResponse {

        $workingSchedule->deleted_by = Auth::id();
        $workingSchedule->save();

        $workingSchedule->delete();

        return redirect()
            ->route('working-schedules.index')
            ->with('success', 'Working schedule deleted successfully.');
    }

    /**
     * Activate / deactivate schedule.
     */
    public function toggleStatus(
        WorkingSchedule $workingSchedule
    ): RedirectResponse {

        $workingSchedule->update([
            'is_active'  => ! $workingSchedule->is_active,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                $workingSchedule->is_active
                    ? 'Working schedule activated successfully.'
                    : 'Working schedule deactivated successfully.'
            );
    }
}