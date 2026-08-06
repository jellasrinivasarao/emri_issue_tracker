<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


use Carbon\Carbon;
use App\Models\WorkingCalendar;
use App\Models\WorkingSchedule;
use App\Models\CalendarHoliday;

class WorkingCalendarService
{

protected WorkingCalendar $calendar;

    protected $schedules;

    protected array $holidays = [];

    public function __construct(WorkingCalendar $calendar) {

        $this->calendar = $calendar;

        $this->loadSchedules(); #Load Schedule

        $this->loadHolidays(); #Load Holidays

    }


    protected function loadSchedules(): void
    {
        $this->schedules = WorkingSchedule::where('calendar_id',$this->calendar->calendar_id)
            ->get()
            ->keyBy(function ($item) {
                return strtolower($item->day_of_week);
            });
    }


    protected function loadHolidays(): void
    {
        $this->holidays = CalendarHoliday::where('calendar_id',$this->calendar->calendar_id)
            ->pluck('holiday_date')
            ->map(function ($date) {
                return Carbon::parse($date)->toDateString();
            })->toArray();
    }

        public function getSchedule(Carbon $date): ?WorkingSchedule{

            return $this->schedules[
                strtolower($date->format('l'))
            ] ?? null;

        }

        public function isHoliday(Carbon $date): bool
        {

            return in_array($date->toDateString(),$this->holidays);

        }

        public function isWorkingDay(Carbon $date): bool
        {

            return !$this->isHoliday($date) && $this->getSchedule($date);

        }

        public function officeStart(Carbon $date): Carbon{

                    $schedule = $this->getSchedule($date);

                    return Carbon::parse($date->toDateString().' '.$schedule->start_time);
        }

        public function officeEnd(Carbon $date): Carbon{

                $schedule = $this->getSchedule($date);

                return Carbon::parse($date->toDateString().' '.$schedule->end_time);

        }
        
        
        public function nextWorkingDay(Carbon $date): Carbon{

                do {

                    $date = $date

                        ->copy()

                        ->addDay()

                        ->startOfDay();

                }

                while (!$this->isWorkingDay($date));

                return $this->officeStart($date);

            }


            public function previousWorkingDay(Carbon $date): Carbon{

                    do {

                        $date = $date

                            ->copy()

                            ->subDay()

                            ->startOfDay();

                    }

                    while (!$this->isWorkingDay($date));

                    return $this->officeStart($date);
                }


                public function moveToWorkingTime(Carbon $date): Carbon{

                        while (true) {

                            if (!$this->isWorkingDay($date)) {

                                $date = $this->nextWorkingDay($date);

                                continue;

                            }

                            $officeStart = $this->officeStart($date);

                            $officeEnd = $this->officeEnd($date);

                            if ($date->lt($officeStart)) {

                                return $officeStart;

                            }

                            if ($date->gte($officeEnd)) {

                                $date = $this->nextWorkingDay($date);

                                continue;

                            }

                            return $date;

                        }

                }


    ############ SLA Logic ENDS ########################
    
    // public function create(array $data): WorkingCalendar
    // {
    //     return DB::transaction(function () use ($data) {

    //         $data['created_by'] = Auth::id();

    //         $calendar = WorkingCalendar::create($data);

    //         return $calendar;
    //     });
    // }

    // public function update(WorkingCalendar $calendar,array $data): WorkingCalendar {

    //     return DB::transaction(function () use ($calendar, $data) {

    //         $data['updated_by'] = Auth::id();

    //         $calendar->update($data);

    //         return $calendar->refresh();
    //     });
    // }

    // public function delete(WorkingCalendar $calendar): void {

    //     DB::transaction(function () use ($calendar) {

    //         $calendar->update([
    //             'deleted_by' => Auth::id(),
    //         ]);

    //         $calendar->delete();
    //     });
    // }

    // public function activate(WorkingCalendar $calendar): void {

    //     $calendar->update([
    //         'is_active' => true,
    //         'updated_by' => Auth::id(),
    //     ]);
    // }

    // public function deactivate(WorkingCalendar $calendar): void {

    //     $calendar->update([
    //         'is_active' => false,
    //         'updated_by' => Auth::id(),
    //     ]);
    // }



    
}