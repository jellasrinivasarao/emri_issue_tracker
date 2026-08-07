<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\WorkingCalendar;
use App\Models\WorkingSchedule;
use App\Models\CalendarHoliday;

class WorkingCalendarService
{
    /*
    |--------------------------------------------------------------------------
    | Calendar
    |--------------------------------------------------------------------------
    */

    protected WorkingCalendar $calendar;


    /*
    |--------------------------------------------------------------------------
    | Caches
    |--------------------------------------------------------------------------
    */

    /**
     * [
     *     'monday' => Collection,
     *     'tuesday' => Collection,
     *     ...
     * ]
     */
    protected array $scheduleCache = [];


    /**
     * [
     *     'YYYY-MM-DD',
     *     ...
     * ]
     */
    protected array $holidayCache = [];


    /**
     * [
     *     shift_no => Collection
     * ]
     */
    protected array $shiftCache = [];


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(WorkingCalendar $calendar)
    {
        $this->calendar = $calendar;

        $this->loadCache();
    }


    /*
    |--------------------------------------------------------------------------
    | Calendar
    |--------------------------------------------------------------------------
    */

    public function getCalendar(): WorkingCalendar
    {
        return $this->calendar;
    }


    public function getCalendarId(): int
    {
        return (int) $this->calendar->calendar_id;
    }


    /*
    |--------------------------------------------------------------------------
    | Load Cache
    |--------------------------------------------------------------------------
    */

    protected function loadCacheOld(): void
    {
        $this->scheduleCache = [];

        $this->holidayCache = [];

        $this->shiftCache = [];


        /*
        |--------------------------------------------------------------------------
        | Working Schedules
        |--------------------------------------------------------------------------
        */

        $schedules = WorkingSchedule::query()
            ->where(
                'calendar_id',
                $this->calendar->calendar_id
            )
            ->where('is_active', 1)
            ->orderBy('day_of_week')
            ->orderBy('shift_no')
            ->get();


        foreach ($schedules as $schedule) {

            /*
            |--------------------------------------------------------------------------
            | Normalize day
            |--------------------------------------------------------------------------
            |
            | Expected values:
            |
            | Monday
            | Tuesday
            | Wednesday
            | Thursday
            | Friday
            | Saturday
            | Sunday
            |
            */

            $day = strtolower(
                trim((string) $schedule->day_of_week)
            );


            /*
            |--------------------------------------------------------------------------
            | Day Cache
            |--------------------------------------------------------------------------
            */

            if (!isset($this->scheduleCache[$day])) {

                $this->scheduleCache[$day] = collect();

            }

            $this->scheduleCache[$day]->push($schedule);


            /*
            |--------------------------------------------------------------------------
            | Shift Cache
            |--------------------------------------------------------------------------
            */

            $shiftNo = (int) ($schedule->shift_no ?? 1);

            if (!isset($this->shiftCache[$shiftNo])) {

                $this->shiftCache[$shiftNo] = collect();

            }

            $this->shiftCache[$shiftNo]->push($schedule);
        }


        /*
        |--------------------------------------------------------------------------
        | Holidays
        |--------------------------------------------------------------------------
        */

        $holidays = CalendarHoliday::query()
            ->where(
                'calendar_id',
                $this->calendar->calendar_id
            )
            ->pluck('holiday_date');


        foreach ($holidays as $holiday) {

            $this->holidayCache[] =
                Carbon::parse($holiday)->toDateString();

        }


        /*
        |--------------------------------------------------------------------------
        | Remove duplicate holidays
        |--------------------------------------------------------------------------
        */

        $this->holidayCache = array_values(
            array_unique($this->holidayCache)
        );
    }

    protected function loadCache(): void
{
    $this->scheduleCache = [];
    $this->holidayCache = [];
    $this->shiftCache = [];

    $schedules = WorkingSchedule::query()
        ->where('calendar_id', $this->calendar->calendar_id)
        ->where('is_active', 1)
        ->orderBy('day_of_week')
        ->orderBy('shift_no')
        ->get();

    \Log::info('Working calendar schedules', [
        'calendar_id' => $this->calendar->calendar_id,
        'count' => $schedules->count(),
        'schedules' => $schedules->map(function ($row) {
            return [
                'schedule_id' => $row->schedule_id ?? null,
                'day_of_week' => $row->day_of_week,
                'shift_no' => $row->shift_no,
                'start_time' => $row->start_time,
                'end_time' => $row->end_time,
                'is_active' => $row->is_active,
            ];
        })->toArray(),
    ]);

    foreach ($schedules as $schedule) {

        // $day = strtolower(
        //     trim((string) $schedule->day_of_week)
        // );

        $day = (int) $schedule->day_of_week;

        if (!isset($this->scheduleCache[$day])) {
            $this->scheduleCache[$day] = collect();
        }

        $this->scheduleCache[$day]->push($schedule);

        $shiftNo = (int) ($schedule->shift_no ?? 1);

        if (!isset($this->shiftCache[$shiftNo])) {
            $this->shiftCache[$shiftNo] = collect();
        }

        $this->shiftCache[$shiftNo]->push($schedule);
    }

    $holidays = CalendarHoliday::query()
        ->where(
            'calendar_id',
            $this->calendar->calendar_id
        )
        ->pluck('holiday_date');

    foreach ($holidays as $holiday) {
        $this->holidayCache[] =
            Carbon::parse($holiday)->toDateString();
    }

    $this->holidayCache = array_values(
        array_unique($this->holidayCache)
    );

    \Log::info('Working calendar cache loaded', [
        'calendar_id' => $this->calendar->calendar_id,
        'schedule_days' => array_keys($this->scheduleCache),
        'holiday_count' => count($this->holidayCache),
    ]);
}

    /*
    |--------------------------------------------------------------------------
    | Refresh Cache
    |--------------------------------------------------------------------------
    */

    public function refreshCache(): void
    {
        $this->loadCache();
    }


    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    */

    public function getSchedule(Carbon $date): ?WorkingSchedule
    {
        $shifts = $this->getShiftsForDate($date);

        return $shifts->first();
    }


    public function getShiftsForDate(Carbon $date)
    {
        // $day = strtolower(
        //     $date->format('l')
        // );

        $day = $date->dayOfWeek;

        return $this->scheduleCache[$day]?? collect();
    }


    /*
    |--------------------------------------------------------------------------
    | Working Day
    |--------------------------------------------------------------------------
    */

    public function isWeekend(Carbon $date): bool
    {
        /*
        |--------------------------------------------------------------------------
        | In this implementation a day without an active schedule
        | is considered a non-working day.
        |--------------------------------------------------------------------------
        */

        return $this->getShiftsForDate($date)->isEmpty();
    }


    public function isHoliday(Carbon $date): bool
    {
        return in_array(
            $date->toDateString(),
            $this->holidayCache,
            true
        );
    }


    public function isWorkingDay(Carbon $date): bool
    {
        if ($this->isHoliday($date)) {
            return false;
        }

        return !$this->isWeekend($date);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Current Shift
    |--------------------------------------------------------------------------
    */

    public function getShift(Carbon $date)
    {
        $shifts = $this->getShiftsForDate($date);

        if ($shifts->isEmpty()) {
            return null;
        }


        foreach ($shifts as $shift) {

            $start = $this->shiftStart(
                $date,
                $shift
            );

            $end = $this->shiftEnd(
                $date,
                $shift
            );


            /*
            |--------------------------------------------------------------------------
            | Current time is inside shift
            |--------------------------------------------------------------------------
            */

            if (
                $date->gte($start) &&
                $date->lt($end)
            ) {

                return $shift;

            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Shift Start
    |--------------------------------------------------------------------------
    */

    protected function shiftStart(
        Carbon $date,
        WorkingSchedule $shift
    ): Carbon {

        return Carbon::parse(
            $date->toDateString()
            . ' '
            . $shift->start_time
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Shift End
    |--------------------------------------------------------------------------
    */

    protected function shiftEnd(
        Carbon $date,
        WorkingSchedule $shift
    ): Carbon {

        $start = $this->shiftStart(
            $date,
            $shift
        );


        $end = Carbon::parse(
            $date->toDateString()
            . ' '
            . $shift->end_time
        );


        /*
        |--------------------------------------------------------------------------
        | Overnight Shift
        |--------------------------------------------------------------------------
        */

        if ($end->lte($start)) {

            $end->addDay();

        }


        return $end;
    }


    /*
    |--------------------------------------------------------------------------
    | Lunch Break
    |--------------------------------------------------------------------------
    */

    public function getLunchBreak(
        Carbon $date
    ): ?array {

        $shift = $this->getShift($date);

        if (!$shift) {

            /*
            |--------------------------------------------------------------------------
            | If current time isn't inside a shift,
            | use the first shift for that day.
            |--------------------------------------------------------------------------
            */

            $shift = $this->getSchedule($date);

        }


        if (!$shift) {
            return null;
        }


        return $this->getLunchBreakForShift(
            $date,
            $shift
        );
    }


    protected function getLunchBreakForShift(
        Carbon $date,
        WorkingSchedule $shift
    ): ?array {

        if (
            empty($shift->break_start) ||
            empty($shift->break_end)
        ) {

            return null;
        }


        $start = Carbon::parse(
            $date->toDateString()
            . ' '
            . $shift->break_start
        );


        $end = Carbon::parse(
            $date->toDateString()
            . ' '
            . $shift->break_end
        );


        /*
        |--------------------------------------------------------------------------
        | Overnight Break
        |--------------------------------------------------------------------------
        */

        if ($end->lte($start)) {

            $end->addDay();

        }


        return [
            'start' => $start,
            'end'   => $end,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Is Office Open
    |--------------------------------------------------------------------------
    */

    public function isOfficeOpen(
        Carbon $dateTime
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Holiday / Weekend
        |--------------------------------------------------------------------------
        */

        if (!$this->isWorkingDay($dateTime)) {

            return false;

        }


        /*
        |--------------------------------------------------------------------------
        | Find Current Shift
        |--------------------------------------------------------------------------
        */

        $shift = $this->getShift($dateTime);

        if (!$shift) {

            return false;

        }


        /*
        |--------------------------------------------------------------------------
        | Shift Time
        |--------------------------------------------------------------------------
        */

        $start = $this->shiftStart(
            $dateTime,
            $shift
        );


        $end = $this->shiftEnd(
            $dateTime,
            $shift
        );


        if (
            $dateTime->lt($start) ||
            $dateTime->gte($end)
        ) {

            return false;

        }


        /*
        |--------------------------------------------------------------------------
        | Lunch Break
        |--------------------------------------------------------------------------
        */

        $break = $this->getLunchBreakForShift(
            $dateTime,
            $shift
        );


        if ($break) {

            if (
                $dateTime->gte($break['start']) &&
                $dateTime->lt($break['end'])
            ) {

                return false;

            }
        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Remaining Office Minutes
    |--------------------------------------------------------------------------
    */

    public function remainingOfficeMinutes(
        Carbon $dateTime
    ): int {

        /*
        |--------------------------------------------------------------------------
        | Find current shift
        |--------------------------------------------------------------------------
        */

        $shift = $this->getShift($dateTime);

        if (!$shift) {

            return 0;

        }


        $end = $this->shiftEnd(
            $dateTime,
            $shift
        );


        /*
        |--------------------------------------------------------------------------
        | If currently inside lunch
        |--------------------------------------------------------------------------
        */

        $break = $this->getLunchBreakForShift(
            $dateTime,
            $shift
        );


        if ($break) {

            if (
                $dateTime->gte($break['start']) &&
                $dateTime->lt($break['end'])
            ) {

                $dateTime = $break['end'];

            }
        }


        /*
        |--------------------------------------------------------------------------
        | Total remaining minutes
        |--------------------------------------------------------------------------
        */

        if ($dateTime->gte($end)) {

            return 0;

        }


        $minutes = $dateTime->diffInMinutes(
            $end
        );


        /*
        |--------------------------------------------------------------------------
        | Subtract future lunch break
        |--------------------------------------------------------------------------
        */

        if (
            $break &&
            $dateTime->lt($break['start']) &&
            $break['start']->lt($end)
        ) {

            $breakMinutes = $break['start']->diffInMinutes(
                $break['end']
            );


            $minutes -= $breakMinutes;

        }


        return max(
            0,
            $minutes
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Working Minutes Between
    |--------------------------------------------------------------------------
    */

    public function workingMinutesBetween(
        Carbon $start,
        Carbon $end
    ): int {

        if ($start->gte($end)) {
            return 0;
        }


        $minutes = 0;

        $current = $start->copy();


        /*
        |--------------------------------------------------------------------------
        | Safety limit
        |--------------------------------------------------------------------------
        */

        $maxIterations = 366 * 24 * 60;

        $iterations = 0;


        while ($current->lt($end)) {

            if ($this->isOfficeOpen($current)) {

                $minutes++;

            }


            $current->addMinute();

            $iterations++;


            if ($iterations > $maxIterations) {

                throw new \RuntimeException(
                    'Working minutes calculation exceeded 366 days.'
                );

            }
        }


        return $minutes;
    }


    /*
    |--------------------------------------------------------------------------
    | Office Start
    |--------------------------------------------------------------------------
    */

    public function officeStart(
        Carbon $date
    ): Carbon {

        $schedule = $this->getSchedule(
            $date
        );


        if (!$schedule) {

            throw new \RuntimeException(
                'No working schedule configured for '
                . $date->format('l')
            );

        }


        return $this->shiftStart(
            $date,
            $schedule
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Office End
    |--------------------------------------------------------------------------
    */

    public function officeEnd(
        Carbon $date
    ): Carbon {

        $schedule = $this->getSchedule(
            $date
        );


        if (!$schedule) {

            throw new \RuntimeException(
                'No working schedule configured for '
                . $date->format('l')
            );

        }


        return $this->shiftEnd(
            $date,
            $schedule
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Next Working Day
    |--------------------------------------------------------------------------
    */

    public function nextWorkingDay(
        Carbon $date
    ): Carbon {

        $current = $date->copy();


        for ($i = 0; $i < 366; $i++) {

            $current
                ->startOfDay()
                ->addDay();


            if (!$this->isWorkingDay($current)) {

                continue;

            }


            return $this->officeStart(
                $current
            );
        }


        throw new \RuntimeException(
            'No next working day found within 366 days for calendar '
            . $this->calendar->calendar_id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Previous Working Day
    |--------------------------------------------------------------------------
    */

    public function previousWorkingDay(
        Carbon $date
    ): Carbon {

        $current = $date->copy();


        for ($i = 0; $i < 366; $i++) {

            $current
                ->startOfDay()
                ->subDay();


            if (!$this->isWorkingDay($current)) {

                continue;

            }


            return $this->officeStart(
                $current
            );
        }


        throw new \RuntimeException(
            'No previous working day found within 366 days for calendar '
            . $this->calendar->calendar_id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Move To Working Time
    |--------------------------------------------------------------------------
    */

    public function moveToWorkingTime(
        Carbon $dateTime
    ): Carbon {

        $current = $dateTime->copy();


        /*
        |--------------------------------------------------------------------------
        | Search maximum 366 days
        |--------------------------------------------------------------------------
        */

        for ($days = 0; $days < 366; $days++) {


            /*
            |--------------------------------------------------------------------------
            | Holiday / Non-working day
            |--------------------------------------------------------------------------
            */

            if (!$this->isWorkingDay($current)) {

                $current = $current
                    ->copy()
                    ->startOfDay()
                    ->addDay();

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Get all shifts for this day
            |--------------------------------------------------------------------------
            */

            $shifts = $this->getShiftsForDate(
                $current
            );


            if ($shifts->isEmpty()) {

                $current = $current
                    ->copy()
                    ->startOfDay()
                    ->addDay();

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Check each shift
            |--------------------------------------------------------------------------
            */

            foreach ($shifts as $shift) {

                $start = $this->shiftStart(
                    $current,
                    $shift
                );


                $end = $this->shiftEnd(
                    $current,
                    $shift
                );


                /*
                |--------------------------------------------------------------------------
                | Before shift
                |--------------------------------------------------------------------------
                */

                if ($current->lt($start)) {

                    return $start;

                }


                /*
                |--------------------------------------------------------------------------
                | After shift
                |--------------------------------------------------------------------------
                */

                if ($current->gte($end)) {

                    continue;

                }


                /*
                |--------------------------------------------------------------------------
                | Inside shift
                |--------------------------------------------------------------------------
                */

                $break = $this->getLunchBreakForShift(
                    $current,
                    $shift
                );


                /*
                |--------------------------------------------------------------------------
                | Inside lunch break
                |--------------------------------------------------------------------------
                */

                if ($break) {

                    if (
                        $current->gte($break['start']) &&
                        $current->lt($break['end'])
                    ) {

                        return $break['end'];

                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Valid working time
                |--------------------------------------------------------------------------
                */

                return $current;
            }


            /*
            |--------------------------------------------------------------------------
            | No remaining shift today
            |--------------------------------------------------------------------------
            */

            $current = $current
                ->copy()
                ->startOfDay()
                ->addDay();
        }


        throw new \RuntimeException(
            'No working time found within 366 days for calendar '
            . $this->calendar->calendar_id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Current Shift
    |--------------------------------------------------------------------------
    */

    public function getCurrentShift(
        Carbon $date
    ) {

        if (!$this->isWorkingDay($date)) {

            return null;

        }


        return $this->getShift(
            $date
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Next Shift
    |--------------------------------------------------------------------------
    */

    public function getNextShift(
        Carbon $date
    ): ?Carbon {

        /*
        |--------------------------------------------------------------------------
        | Remaining shifts today
        |--------------------------------------------------------------------------
        */

        if ($this->isWorkingDay($date)) {

            $shifts = $this->getShiftsForDate(
                $date
            );


            foreach ($shifts as $shift) {

                $start = $this->shiftStart(
                    $date,
                    $shift
                );


                if ($start->gt($date)) {

                    return $start;

                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Search next working day
        |--------------------------------------------------------------------------
        */

        $nextDay = $date->copy();


        for ($i = 0; $i < 366; $i++) {

            $nextDay
                ->startOfDay()
                ->addDay();


            if (!$this->isWorkingDay($nextDay)) {

                continue;

            }


            $shifts = $this->getShiftsForDate(
                $nextDay
            );


            $shift = $shifts->first();


            if (!$shift) {

                continue;

            }


            return $this->shiftStart(
                $nextDay,
                $shift
            );
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Shift By Number
    |--------------------------------------------------------------------------
    */

    public function getShiftsByNumber(
        int $shiftNo
    ) {

        return $this->shiftCache[$shiftNo]
            ?? collect();
    }


    /*
    |--------------------------------------------------------------------------
    | Cache Debugging
    |--------------------------------------------------------------------------
    */

    public function getScheduleCache(): array
    {
        return $this->scheduleCache;
    }


    public function getHolidayCache(): array
    {
        return $this->holidayCache;
    }


    /*
    |--------------------------------------------------------------------------
    | CRUD - Optional
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data
    ): WorkingCalendar {

        return DB::transaction(
            function () use ($data) {

                $data['created_by'] =
                    Auth::id();

                return WorkingCalendar::create(
                    $data
                );
            }
        );
    }


    public function update(
        WorkingCalendar $calendar,
        array $data
    ): WorkingCalendar {

        return DB::transaction(
            function () use (
                $calendar,
                $data
            ) {

                $data['updated_by'] =
                    Auth::id();

                $calendar->update(
                    $data
                );

                return $calendar->refresh();
            }
        );
    }


    public function delete(
        WorkingCalendar $calendar
    ): void {

        DB::transaction(
            function () use ($calendar) {

                $calendar->update([
                    'deleted_by' =>
                        Auth::id(),
                ]);

                $calendar->delete();
            }
        );
    }


    public function activate(
        WorkingCalendar $calendar
    ): void {

        $calendar->update([
            'is_active' => true,
            'updated_by' =>
                Auth::id(),
        ]);
    }


    public function deactivate(
        WorkingCalendar $calendar
    ): void {

        $calendar->update([
            'is_active' => false,
            'updated_by' =>
                Auth::id(),
        ]);
    }
}