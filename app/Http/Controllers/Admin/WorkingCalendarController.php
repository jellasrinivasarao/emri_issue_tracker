<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkingCalendar;
use App\Http\Requests\WorkingCalendarStoreRequest;
use App\Http\Requests\WorkingCalendarUpdateRequest;
use Illuminate\Http\RedirectResponse;

class WorkingCalendarController extends Controller
{
    public function store(WorkingCalendarStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        WorkingCalendar::create([
            'calendar_code' => $data['calendar_code'],
            'calendar_name' => $data['calendar_name'],
            'timezone' => $data['timezone'] ?? config('app.timezone'),
            'organisation_id' => $data['organisation_id'] ?? null,
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
        ]);

        return redirect()->route('working.hours')->with('success', 'Working calendar created.');
    }

    public function update(WorkingCalendarUpdateRequest $request, $calendar_id): RedirectResponse
    {
        $calendar = WorkingCalendar::findOrFail($calendar_id);

        $data = $request->validated();

        $calendar->update([
            'calendar_code' => $data['calendar_code'],
            'calendar_name' => $data['calendar_name'],
            'timezone' => $data['timezone'] ?? $calendar->timezone,
            'organisation_id' => $data['organisation_id'] ?? $calendar->organisation_id,
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : $calendar->is_active,
        ]);

        return redirect()->route('working.hours')->with('success', 'Working calendar updated.');
    }

    public function destroy($calendar_id): RedirectResponse
    {
        $calendar = WorkingCalendar::findOrFail($calendar_id);

        // soft-delete not configured; perform delete
        $calendar->delete();

        return redirect()->route('working.hours')->with('success', 'Working calendar deleted.');
    }
}
