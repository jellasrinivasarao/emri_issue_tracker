<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkingCalendarStoreRequest;
use App\Http\Requests\WorkingCalendarUpdateRequest;
use App\Models\State;
use App\Models\WorkingCalendar;
use App\Models\Organisation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkingCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $query = WorkingCalendar::query()->with(['organisation', 'state']);

        if ($search = $request->input('search')) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('calendar_code', 'LIKE', "%{$search}%")
                    ->orWhere('calendar_name', 'LIKE', "%{$search}%");
            });
        }

        if ($org = $request->input('organisation_id')) {
            $query->where('organisation_id', $org);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status'));
        }

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->input('state_id'));
        }

        $calendars = $query->orderBy('calendar_name')->paginate(20)->withQueryString();

        $organisations = Organisation::query()
            ->where('is_active', true)
            ->orderBy('organisation_name')
            ->get(['organisation_id', 'organisation_name']);

        $states = State::query()
            ->where('is_active', true)
            ->orderBy('state_name')
            ->get(['state_id', 'state_name']);

        return view('admin.operational.working-hours', compact('calendars', 'organisations', 'states'));
    }

    public function store(WorkingCalendarStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        WorkingCalendar::create([
            'calendar_code' => strtoupper($data['calendar_code']),
            'calendar_name' => $data['calendar_name'],
            'timezone' => $data['timezone'] ?? config('app.timezone'),
            'organisation_id' => $data['organisation_id'] ?? null,
            'state_id' => $data['state_id'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('working.calendars')->with('success', 'Working calendar created.');
    }

    public function update(WorkingCalendarUpdateRequest $request, $calendar_id): RedirectResponse
    {
        $calendar = WorkingCalendar::findOrFail($calendar_id);

        $data = $request->validated();

        $calendar->update([
            'calendar_code' => strtoupper($data['calendar_code']),
            'calendar_name' => $data['calendar_name'],
            'timezone' => $data['timezone'] ?? $calendar->timezone,
            'organisation_id' => $data['organisation_id'] ?? $calendar->organisation_id,
            'state_id' => $data['state_id'] ?? null,
            'is_active' => $request->boolean('is_active', $calendar->is_active),
        ]);

        return redirect()->route('working.calendars')->with('success', 'Working calendar updated.');
    }

    public function toggle($calendar_id): RedirectResponse
    {
        $calendar = WorkingCalendar::findOrFail($calendar_id);
        $calendar->is_active = !$calendar->is_active;
        $calendar->save();

        return redirect()->route('working.calendars')->with('success', 'Calendar status updated.');
    }

    public function destroy($calendar_id): RedirectResponse
    {
        $calendar = WorkingCalendar::findOrFail($calendar_id);
        $calendar->delete();

        return redirect()->route('working.calendars')->with('success', 'Working calendar deleted.');
    }
}