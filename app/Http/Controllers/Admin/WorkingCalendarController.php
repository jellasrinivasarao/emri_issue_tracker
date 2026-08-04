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

class WorkingCalendarController extends Controller
{
    public function __construct(protected WorkingCalendarService $service) {}

    public function index(): View
    {
        $calendars = WorkingCalendar::query()
            ->latest('calendar_id')
            ->paginate(20);

        return view(
            'admin.working-calendars.index',
            compact('calendars')
        );
    }

    public function create(): View
    {
        return view(
            'admin.working-calendars.create'
        );
    }

    public function store(
        WorkingCalendarStoreRequest $request
    ): RedirectResponse {

        $this->service->create(
            $request->validated()
        );

        return redirect()
            ->route('admin.working-calendars.index')
            ->with('success', 'Working calendar created successfully.');
    }

    public function edit(
        WorkingCalendar $calendar
    ): View {

        return view(
            'admin.working-calendars.edit',
            compact('calendar')
        );
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
            ->route('admin.working-calendars.index')
            ->with('success', 'Working calendar updated successfully.');
    }

    public function destroy(
        WorkingCalendar $calendar
    ): RedirectResponse {

        $this->service->delete($calendar);

        return back()->with(
            'success',
            'Working calendar deleted successfully.'
        );
    }
}