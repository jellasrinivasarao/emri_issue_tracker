<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CalendarHoliday;
use App\Models\WorkingCalendar;

use Illuminate\Support\Facades\DB;
use Throwable;

class CalendarHolidayController extends Controller
{
        /**
     * Display holiday master.
     */
    public function index(Request $request)
    {
        $query = CalendarHoliday::query()
            ->with('calendar');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'holiday_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'holiday_type',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                );

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
        | Holiday Type Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('holiday_type')) {

            $query->where(
                'holiday_type',
                $request->holiday_type
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Active Filter
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
        | Date Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('from_date')) {

            $query->whereDate(
                'holiday_date',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {

            $query->whereDate(
                'holiday_date',
                '<=',
                $request->to_date
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Ordering
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            'holiday_date',
            'asc'
        );

        $query->orderBy(
            'holiday_name',
            'asc'
        );


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $holidays = $query
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
            'admin.calendar-holidays.index',
            [
                'title' =>
                    'Calendar Holiday Master',

                'description' =>
                    'Manage holidays and non-working dates configured for working calendars.',

                'holidays' =>
                    $holidays,

                'calendars' =>
                    $calendars,
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
            'admin.calendar-holidays.create',
            [
                'title' =>
                    'Create Calendar Holiday',

                'calendars' =>
                    $calendars,

                'selectedCalendarId' =>
                    $request->calendar_id,
            ]
        );
    }


    /**
     * Store holiday.
     */
    public function store(Request $request)
    {
        $validated =
            $this->validateHoliday($request);


        try {

            DB::transaction(function () use ($validated) {

                /*
                |--------------------------------------------------------------------------
                | Validate Calendar
                |--------------------------------------------------------------------------
                |
                | No database foreign key.
                | Validate manually.
                |
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
                | Prevent Duplicate Holiday
                |--------------------------------------------------------------------------
                */

                $duplicate =
                    CalendarHoliday::query()
                        ->where(
                            'calendar_id',
                            $validated['calendar_id']
                        )
                        ->whereDate(
                            'holiday_date',
                            $validated['holiday_date']
                        )
                        ->exists();


                if ($duplicate) {

                    throw new \RuntimeException(
                        'A holiday already exists for the selected calendar and date.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Create Holiday
                |--------------------------------------------------------------------------
                */

                CalendarHoliday::create([

                    'calendar_id' =>
                        $validated['calendar_id'],

                    'holiday_date' =>
                        $validated['holiday_date'],

                    'holiday_name' =>
                        trim($validated['holiday_name']),

                    'holiday_type' =>
                        $validated['holiday_type'] ?? 'PUBLIC',

                    'description' =>
                        isset($validated['description'])
                            ? trim($validated['description'])
                            : null,

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
                    $e instanceof \RuntimeException
                        ? $e->getMessage()
                        : 'Unable to create calendar holiday.'
                );
        }


        return redirect()
            ->route('calendar-holidays.index')
            ->with(
                'success',
                'Calendar holiday created successfully.'
            );
    }


    /**
     * Display holiday.
     */
    public function show(CalendarHoliday $holiday)
    {
        $holiday =
            CalendarHoliday::query()
                ->with('calendar')
                ->findOrFail($holiday->holiday_id);

        return view(
            'admin.calendar-holidays.show',
            [
                'title' =>
                    'Calendar Holiday Details',

                'holiday' =>
                    $holiday,
            ]
        );
    }


    /**
     * Update holiday.
     */
    public function update(Request $request, CalendarHoliday $holiday) {

        $holiday =
            CalendarHoliday::findOrFail(
                $holiday->holiday_id
            );


        $validated =
            $this->validateHoliday(
                $request,
                $holiday->holiday_id
            );


        try {

            DB::transaction(function () use (
                $validated,
                $holiday
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
                | Duplicate Check
                |--------------------------------------------------------------------------
                */

                $duplicate =
                    CalendarHoliday::query()
                        ->where(
                            'calendar_id',
                            $validated['calendar_id']
                        )
                        ->whereDate(
                            'holiday_date',
                            $validated['holiday_date']
                        )
                        ->where(
                            'holiday_id',
                            '!=',
                            $holiday->holiday_id
                        )
                        ->exists();


                if ($duplicate) {

                    throw new \RuntimeException(
                        'Another holiday already exists for the selected calendar and date.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Update
                |--------------------------------------------------------------------------
                */

                $holiday->update([

                    'calendar_id' =>
                        $validated['calendar_id'],

                    'holiday_date' =>
                        $validated['holiday_date'],

                    'holiday_name' =>
                        trim($validated['holiday_name']),

                    'holiday_type' =>
                        $validated['holiday_type'] ?? 'PUBLIC',

                    'description' =>
                        isset($validated['description'])
                            ? trim($validated['description'])
                            : null,

                ]);
            });

        } catch (Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e instanceof \RuntimeException
                        ? $e->getMessage()
                        : 'Unable to update calendar holiday.'
                );
        }


        return redirect()
            ->route('calendar-holidays.index')
            ->with(
                'success',
                'Calendar holiday updated successfully.'
            );
    }


    /**
     * Activate / Deactivate holiday.
     */
    public function toggle($holiday_id)
    {
        $holiday =
            CalendarHoliday::findOrFail(
                $holiday_id
            );


        try {

            DB::transaction(function () use (
                $holiday
            ) {

                $holiday->update([

                    'is_active' =>
                        !((int) $holiday->is_active),

                ]);
            });

        } catch (Throwable $e) {

            report($e);

            return back()
                ->with(
                    'error',
                    'Unable to change holiday status.'
                );
        }


        return redirect()
            ->route('calendar-holidays.index')
            ->with(
                'success',
                $holiday->is_active
                    ? 'Calendar holiday activated successfully.'
                    : 'Calendar holiday disabled successfully.'
            );
    }


    /**
     * Validate holiday request.
     */
    private function validateHoliday(
        Request $request,
        ?int $holidayId = null
    ): array {

        return $request->validate([

            'calendar_id' => [
                'required',
                'integer',
            ],

            'holiday_date' => [
                'required',
                'date',
            ],

            'holiday_name' => [
                'required',
                'string',
                'max:150',
            ],

            'holiday_type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

        ]);
    }
}