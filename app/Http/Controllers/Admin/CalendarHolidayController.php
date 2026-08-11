<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarHoliday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\WorkingCalendar;
use App\Models\Organisation;
use Illuminate\View\View;

class CalendarHolidayController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'holiday_name' => 'required|string|max:255',
            'holiday_date' => 'required|date',
            'scope' => 'nullable|string|max:255',
            'calendar_id' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        CalendarHoliday::create([
            'calendar_id' => $data['calendar_id'] ?? null,
            'holiday_date' => $data['holiday_date'],
            'holiday_name' => $data['holiday_name'],
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
        ]);

        return redirect()->route('holiday.calendar')->with('success', 'Holiday created.');
    }

    public function index(Request $request): View
    {
        $query = CalendarHoliday::query()->with('calendar');

        if ($search = $request->input('search')) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('holiday_name', 'LIKE', "%{$search}%")
                    ->orWhere('holiday_date', 'LIKE', "%{$search}%");
            });
        }

        $holidays = $query->orderBy('holiday_date', 'desc')->paginate(20)->withQueryString();

        $organisations = Organisation::query()->where('is_active', true)->orderBy('organisation_name')->get(['organisation_id','organisation_name']);

        $calendars = WorkingCalendar::query()->orderBy('calendar_name')->get(['calendar_id','calendar_name']);

        return view('admin.operational.holiday-calendar', compact('holidays','organisations','calendars'));
    }

    public function update(Request $request, $holiday_id): RedirectResponse
    {
        $holiday = CalendarHoliday::findOrFail($holiday_id);

        $data = $request->validate([
            'holiday_name' => 'required|string|max:255',
            'holiday_date' => 'required|date',
            'scope' => 'nullable|string|max:255',
            'calendar_id' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $holiday->update([
            'calendar_id' => $data['calendar_id'] ?? $holiday->calendar_id,
            'holiday_date' => $data['holiday_date'],
            'holiday_name' => $data['holiday_name'],
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : $holiday->is_active,
        ]);

        return redirect()->route('holiday.calendar')->with('success', 'Holiday updated.');
    }

    public function toggle($holiday_id): RedirectResponse
    {
        $holiday = CalendarHoliday::findOrFail($holiday_id);
        $holiday->is_active = !$holiday->is_active;
        $holiday->save();

        return redirect()->route('holiday.calendar')->with('success', 'Holiday status updated.');
    }

    public function destroy($holiday_id): RedirectResponse
    {
        $holiday = CalendarHoliday::findOrFail($holiday_id);
        $holiday->delete();

        return redirect()->route('holiday.calendar')->with('success', 'Holiday deleted.');
    }
}
