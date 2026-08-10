<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkingCalendar;
use App\Models\WorkingSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class WorkingScheduleController extends Controller
{
    /**
     * Display working schedules.
     */
    public function index(Request $request)
    {
        $query = WorkingSchedule::query()->with('calendar');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('schedule_name', 'like', "%{$search}%")
                    ->orWhere('day_of_week', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Calendar Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('calendar_id')) {

            $query->where(
                'calendar_id',
                $request->calendar_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('is_active')) {

            $query->where(
                'is_active',
                (int) $request->is_active
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ordering
        |--------------------------------------------------------------------------
        |
        | Monday -> Sunday
        |
        */

        $query->orderByRaw("
            CASE day_of_week
                WHEN 1 THEN 1
                WHEN 2 THEN 2
                WHEN 3 THEN 3
                WHEN 4 THEN 4
                WHEN 5 THEN 5
                WHEN 6 THEN 6
                WHEN 7 THEN 7
                ELSE 8
            END
        ");

        $query->orderBy('start_time');

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $schedules = $query
            ->paginate(25)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Calendar Dropdown
        |--------------------------------------------------------------------------
        */

        $calendars = WorkingCalendar::query()
            ->where('is_active', 1)
            ->orderBy('calendar_name')
            ->get([
                'calendar_id',
                'calendar_code',
                'calendar_name',
            ]);

        return view(
            'admin.working-schedules.index',
            [
                'title' => 'Working Schedule Master',

                'description' =>
                    'Manage working days, working hours and schedules configured for each working calendar.',

                'schedules' => $schedules,

                'calendars' => $calendars,
            ]
        );
    }


    /**
     * Show create page.
     */
    public function create(Request $request)
    {
        $calendars = WorkingCalendar::query()
            ->where('is_active', 1)
            ->orderBy('calendar_name')
            ->get([
                'calendar_id',
                'calendar_code',
                'calendar_name',
            ]);

        return view(
            'admin.working-schedules.create',
            [
                'title' => 'Create Working Schedule',

                'calendars' => $calendars,

                'selectedCalendarId' =>
                    $request->calendar_id,
            ]
        );
    }


    /**
     * Store working schedule.
     */
    public function store(Request $request)
    {
        $validated = $this->validateSchedule(
            $request
        );

        try {

            DB::transaction(function () use ($validated) {

                /*
                |--------------------------------------------------------------------------
                | Validate Calendar
                |--------------------------------------------------------------------------
                |
                | No foreign key is used.
                | Therefore we validate manually.
                |
                */

                $calendar = WorkingCalendar::query()
                    ->where(
                        'calendar_id',
                        $validated['calendar_id']
                    )
                    ->where(
                        'is_active',
                        1
                    )
                    ->first();

                if (!$calendar) {

                    throw new \RuntimeException(
                        'Selected working calendar is invalid or inactive.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Validate Schedule Overlap
                |--------------------------------------------------------------------------
                */

                $this->validateScheduleOverlap(
                    $validated
                );


                /*
                |--------------------------------------------------------------------------
                | Create
                |--------------------------------------------------------------------------
                */

                WorkingSchedule::create([

                    'calendar_id' =>
                        $validated['calendar_id'],

                    'schedule_name' =>
                        trim($validated['schedule_name']),

                    'day_of_week' =>
                        $validated['day_of_week'],

                    'start_time' =>
                        $validated['start_time'],

                    'end_time' =>
                        $validated['end_time'],

                    'is_working_day' =>
                        $validated['is_working_day'] ?? 1,

                    'is_active' =>
                        $validated['is_active'] ?? 1,

                ]);
            });

        } catch (Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create working schedule. Please try again.'
                );
        }

        return redirect()
            ->route('working-schedules.index')
            ->with(
                'success',
                'Working schedule created successfully.'
            );
    }


    /**
     * Display schedule.
     */
    public function show($schedule_id)
    {
        $schedule = WorkingSchedule::query()
            ->with('calendar')
            ->findOrFail($schedule_id);

        return view(
            'admin.working-schedules.show',
            [
                'title' =>
                    'Working Schedule Details',

                'schedule' =>
                    $schedule,
            ]
        );
    }


    /**
     * Update working schedule.
     */
    public function update(Request $request,$schedule_id
    ) {

        $schedule =
            WorkingSchedule::findOrFail(
                $schedule_id
            );

        $validated =
            $this->validateSchedule(
                $request,
                $schedule->schedule_id
            );

        try {

            DB::transaction(function () use (
                $validated,
                $schedule
            ) {

                /*
                |--------------------------------------------------------------------------
                | Validate Calendar
                |--------------------------------------------------------------------------
                */

                $calendar =
                    WorkingCalendar::query()
                        ->where(
                            'calendar_id',
                            $validated['calendar_id']
                        )
                        ->where(
                            'is_active',
                            1
                        )
                        ->first();

                if (!$calendar) {

                    throw new \RuntimeException(
                        'Selected working calendar is invalid or inactive.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Validate Overlap
                |--------------------------------------------------------------------------
                */

                $this->validateScheduleOverlap(
                    $validated,
                    $schedule->schedule_id
                );


                /*
                |--------------------------------------------------------------------------
                | Update
                |--------------------------------------------------------------------------
                */

                $schedule->update([

                    'calendar_id' =>
                        $validated['calendar_id'],

                    'schedule_name' =>
                        trim($validated['schedule_name']),

                    'day_of_week' =>
                        $validated['day_of_week'],

                    'start_time' =>
                        $validated['start_time'],

                    'end_time' =>
                        $validated['end_time'],

                    'is_working_day' =>
                        $validated['is_working_day'] ?? 1,

                ]);
            });

        } catch (Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update working schedule.'
                );
        }

        return redirect()
            ->route('working-schedules.index')
            ->with(
                'success',
                'Working schedule updated successfully.'
            );
    }


    /**
     * Activate / Deactivate schedule.
     */
    public function toggle($schedule_id)
    {
        $schedule =
            WorkingSchedule::findOrFail(
                $schedule_id
            );

        try {

            DB::transaction(function () use (
                $schedule
            ) {

                $schedule->update([

                    'is_active' =>
                        !((int) $schedule->is_active),

                ]);
            });

        } catch (Throwable $e) {

            report($e);

            return back()
                ->with(
                    'error',
                    'Unable to change schedule status.'
                );
        }

        return redirect()
            ->route('working-schedules.index')
            ->with(
                'success',
                $schedule->is_active
                    ? 'Working schedule activated successfully.'
                    : 'Working schedule disabled successfully.'
            );
    }


    /**
     * Validate working schedule.
     */
    private function validateSchedule(
        Request $request,
        ?int $scheduleId = null
    ): array {

        return $request->validate([

            'calendar_id' => [
                'required',
                'integer',
            ],

            'schedule_name' => [
                'required',
                'string',
                'max:150',
            ],

            'day_of_week' => [
                'required',
                'integer',
                'between:1,7',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
            ],

            'is_working_day' => [
                'nullable',
                'boolean',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

        ]);
    }


    /**
     * Prevent overlapping schedules.
     */
    private function validateScheduleOverlap(
        array $data,
        ?int $ignoreScheduleId = null
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Non-working day
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['is_working_day']) &&
            !(int) $data['is_working_day']
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Convert Time
        |--------------------------------------------------------------------------
        */

        $start =
            strtotime(
                $data['start_time']
            );

        $end =
            strtotime(
                $data['end_time']
            );


        /*
        |--------------------------------------------------------------------------
        | Validate Time Range
        |--------------------------------------------------------------------------
        */

        if ($start === $end) {

            throw new \RuntimeException(
                'Start time and end time cannot be the same.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Overnight Schedule
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | 22:00 -> 06:00
        |
        | This is allowed.
        |
        */

        $query =
            WorkingSchedule::query()
                ->where(
                    'calendar_id',
                    $data['calendar_id']
                )
                ->where(
                    'day_of_week',
                    $data['day_of_week']
                )
                ->where(
                    'is_working_day',
                    1
                )
                ->where(
                    'is_active',
                    1
                );


        if ($ignoreScheduleId) {

            $query->where(
                'schedule_id',
                '!=',
                $ignoreScheduleId
            );
        }


        $existingSchedules =
            $query->get();


        foreach (
            $existingSchedules as $existing
        ) {

            if (
                $this->timeRangesOverlap(
                    $data['start_time'],
                    $data['end_time'],
                    $existing->start_time,
                    $existing->end_time
                )
            ) {

                throw new \RuntimeException(
                    sprintf(
                        'Schedule overlaps with existing schedule "%s" (%s - %s).',
                        $existing->schedule_name,
                        $existing->start_time,
                        $existing->end_time
                    )
                );
            }
        }
    }


    /**
     * Determine whether two time ranges overlap.
     */
    private function timeRangesOverlap(
        string $start1,
        string $end1,
        string $start2,
        string $end2
    ): bool {

        $start1 =
            $this->timeToMinutes($start1);

        $end1 =
            $this->timeToMinutes($end1);

        $start2 =
            $this->timeToMinutes($start2);

        $end2 =
            $this->timeToMinutes($end2);


        /*
        |--------------------------------------------------------------------------
        | Convert overnight schedules
        |--------------------------------------------------------------------------
        */

        if ($end1 <= $start1) {
            $end1 += 1440;
        }

        if ($end2 <= $start2) {
            $end2 += 1440;
        }


        /*
        |--------------------------------------------------------------------------
        | Compare ranges
        |--------------------------------------------------------------------------
        */

        return (
            $start1 < $end2 &&
            $start2 < $end1
        );
    }


    /**
     * Convert HH:MM into minutes.
     */
    private function timeToMinutes(
        string $time
    ): int {

        [$hour, $minute] =
            array_map(
                'intval',
                explode(':', $time)
            );

        return (
            ($hour * 60) +
            $minute
        );
    }
}