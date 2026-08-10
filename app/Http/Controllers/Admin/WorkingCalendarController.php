<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\WorkingCalendarStoreRequest;
use App\Http\Requests\WorkingCalendarUpdateRequest;
use App\Models\WorkingCalendar;
use App\Services\WorkingCalendarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class WorkingCalendarController extends Controller
{
    public function __construct(protected WorkingCalendarService $service) {}

    public function index(Request $request): View
    {

        $query = WorkingCalendar::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('calendar_code', 'like', "%{$search}%")
                    ->orWhere('calendar_name', 'like', "%{$search}%")
                    ->orWhere('timezone', 'like', "%{$search}%");
            });
        }

        $calendars = $query
            ->orderBy('calendar_name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.working-calendars.index', [
            'title' => 'Working Calendar Master',
            'description' => 'Manage working calendars, schedules and holiday configurations.',
            'calendars' => $calendars,
        ]);
    }

    public function create(): View
    {
        return view('admin.working-calendars.create',[
            'title' => 'Create Working Calendar',
        ]);
    }

    public function store(WorkingCalendarStoreRequest $request): RedirectResponse {

        $this->service->create(
            $request->validated()
        );

        return redirect()
            ->route('working-calendars.index')
            ->with('success', 'Working calendar created successfully.');
    }

    public function edit(WorkingCalendar $calendar): View {

        return view('admin.working-calendars.edit',compact('calendar'));
    }

    public function update(
        WorkingCalendarUpdateRequest $request,
        WorkingCalendar $calendar
    ): RedirectResponse {

        $this->service->update(
            $calendar,
            $request->validated()
        );

        return redirect()
            ->route('working-calendars.index')
            ->with('success', 'Working calendar updated successfully.');
    }

    public function destroy(WorkingCalendar $calendar): RedirectResponse {

        $this->service->delete($calendar);

        return back()->with(
            'success',
            'Working calendar deleted successfully.'
        );
    }

    public function toggle($calendar_id)
    {
        $calendar = WorkingCalendar::findOrFail($calendar_id);

        DB::transaction(function () use ($calendar) {

            $calendar->update([
                'is_active' => !((int) $calendar->is_active),
            ]);
        });

        return redirect()
            ->route('working-calendars.index')
            ->with(
                'success',
                $calendar->is_active
                    ? 'Working calendar activated successfully.'
                    : 'Working calendar disabled successfully.'
            );
    }
}