<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkingCalendar;
use App\Models\WorkingSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Validation\Rule;
use Illuminate\View\View;

class WorkingScheduleController extends Controller
{
    private array $daysOfWeek = [
        'MONDAY',
        'TUESDAY',
        'WEDNESDAY',
        'THURSDAY',
        'FRIDAY',
        'SATURDAY',
        'SUNDAY',
    ];

    public function index(Request $request, int $calendar_id): View
    {
        $calendar = WorkingCalendar::findOrFail($calendar_id);

        $query = WorkingSchedule::query()
            ->where('calendar_id', $calendar_id)
            ->orderByRaw("FIELD(day_of_week,'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('sequence_no');

        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($sub) use ($search) {
                $sub->where('day_of_week', 'LIKE', "%{$search}%")
                    ->orWhere('schedule_name', 'LIKE', "%{$search}%")
                    ->orWhere('shift_name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status'));
        }

        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->input('day_of_week'));
        }

        $schedules = $query->paginate(20)->withQueryString();

        $routeName = 'working.calendars.schedules';
        $permissions = [
            'view' => auth()->user()?->hasPrivilegeOnRoute($routeName, 'view'),
            'create' => auth()->user()?->hasPrivilegeOnRoute($routeName, 'create'),
            'edit' => auth()->user()?->hasPrivilegeOnRoute($routeName, 'edit'),
            'delete' => auth()->user()?->hasPrivilegeOnRoute($routeName, 'delete'),
            'activate' => auth()->user()?->hasPrivilegeOnRoute($routeName, 'activate'),
            'deactivate' => auth()->user()?->hasPrivilegeOnRoute($routeName, 'deactivate'),
        ];

        return view('admin.operational.working-schedule', [
            'calendar' => $calendar,
            'schedules' => $schedules,
            'daysOfWeek' => $this->daysOfWeek,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request, int $calendar_id): RedirectResponse
    {
        $calendar = WorkingCalendar::findOrFail($calendar_id);
        $data = $this->validateData($request, $calendar_id);

        $data['calendar_id'] = $calendar->calendar_id;
        $data['is_active'] = $request->boolean('is_active', true);

        if (! $data['is_working_day']) {
            $data['start_time'] = null;
            $data['end_time'] = null;
            $data['break_start'] = null;
            $data['break_end'] = null;
        }

        if ($data['is_24_hours']) {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

        WorkingSchedule::create($data);

        return redirect()->route('working.calendars.schedules', ['calendar_id' => $calendar->calendar_id])
            ->with('success', 'Working schedule saved.');
    }

    public function update(Request $request, int $calendar_id, int $schedule_id): RedirectResponse
    {
        $calendar = WorkingCalendar::findOrFail($calendar_id);
        $schedule = WorkingSchedule::where('calendar_id', $calendar_id)->findOrFail($schedule_id);

        $data = $this->validateData($request, $calendar_id, $schedule_id);
        $data['is_active'] = $request->boolean('is_active', $schedule->is_active);

        if (! $data['is_working_day']) {
            $data['start_time'] = null;
            $data['end_time'] = null;
            $data['break_start'] = null;
            $data['break_end'] = null;
        }

        if ($data['is_24_hours']) {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

        $schedule->update($data);

        return redirect()->route('working.calendars.schedules', ['calendar_id' => $calendar->calendar_id])
            ->with('success', 'Working schedule updated.');
    }

    public function toggle(int $calendar_id, int $schedule_id): RedirectResponse
    {
        $schedule = WorkingSchedule::where('calendar_id', $calendar_id)->findOrFail($schedule_id);
        $schedule->is_active = ! $schedule->is_active;
        $schedule->save();

        return redirect()->route('working.calendars.schedules', ['calendar_id' => $calendar_id])
            ->with('success', 'Schedule status updated.');
    }

    public function destroy(int $calendar_id, int $schedule_id): RedirectResponse
    {
        $schedule = WorkingSchedule::where('calendar_id', $calendar_id)->findOrFail($schedule_id);
        $schedule->delete();

        return redirect()->route('working.calendars.schedules', ['calendar_id' => $calendar_id])
            ->with('success', 'Working schedule deleted.');
    }

    private function validateData(Request $request, int $calendar_id, int $schedule_id = null): array
    {
        $rules = [
            'day_of_week' => [
                'required',
                Rule::in($this->daysOfWeek),
                Rule::unique('mst_working_schedule')->where(function ($query) use ($calendar_id, $request) {
                    return $query->where('calendar_id', $calendar_id)
                        ->where('day_of_week', $request->input('day_of_week'))
                        ->where('sequence_no', $request->input('sequence_no', 1));
                })->ignore($schedule_id, 'schedule_id'),
            ],
            'schedule_name' => ['required', 'string', 'max:100'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'break_start' => ['nullable', 'date_format:H:i'],
            'break_end' => ['nullable', 'date_format:H:i'],
            'is_working_day' => ['sometimes', 'boolean'],
            'is_24_hours' => ['sometimes', 'boolean'],
            'sequence_no' => ['required', 'integer', 'min:1'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'shift_no' => ['nullable', 'integer', 'min:1'],
            'shift_name' => ['nullable', 'string', 'max:50'],
        ];

        $validated = $request->validate($rules);
        $validated['is_working_day'] = $request->boolean('is_working_day', false);
        $validated['is_24_hours'] = $request->boolean('is_24_hours', false);

        return $validated;
    }
}