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

    protected array $holidayCache=[];

    protected array $scheduleCache=[];

    protected array $shiftCache=[];
    

    protected $schedules;

    protected array $holidays = [];

    public function __construct(WorkingCalendar $calendar) {

        $this->calendar = $calendar;

        $this->loadCache();

        $this->loadSchedules(); #Load Schedule

        $this->loadHolidays(); #Load Holidays

    }


    protected function loadCache(): void
    {
        /*
        One database hit only.
        */

        $schedules=WorkingSchedule::where(
            'calendar_id',
            $this->calendar->calendar_id
        )
        ->orderBy('shift_no')
        ->get();

        foreach($schedules as $row){

            $day=strtolower($row->day_of_week);

            $this->scheduleCache[$day][]=$row;

            $this->shiftCache[$row->shift_no][]=$row;

        }
        $this->holidayCache=
            CalendarHoliday::where(
                'calendar_id',
                $this->calendar->calendar_id
            )
            ->pluck('holiday_date')
            ->map(fn($d)=>Carbon::parse($d)->toDateString())
            ->toArray();
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

        public function isWeekend(Carbon $date): bool
        {
            $day=strtolower($date->format('l'));

            return empty($this->scheduleCache[$day]);
        }


        public function isHoliday(Carbon $date): bool
        {

            return in_array($date->toDateString(),$this->holidays);

        }

        public function isWorkingDay(Carbon $date): bool
        {

            return !$this->isWeekend($date) && !$this->isHoliday($date);
            #return !$this->isHoliday($date) && $this->getSchedule($date);

        }

        public function getShift(
    Carbon $date
)
{
    $day=strtolower($date->format('l'));

    if(
        empty($this->scheduleCache[$day])
    ){

        return null;

    }

    foreach($this->scheduleCache[$day] as $shift){

        $start=Carbon::parse(
            $date->toDateString().' '.$shift->start_time
        );

        $end=Carbon::parse(
            $date->toDateString().' '.$shift->end_time
        );

        if($shift->end_time < $shift->start_time){
            $end->addDay();
        }

        if($date->betweenIncluded($start,$end)){
            return $shift;
        }

    }

    return $this->scheduleCache[$day][0];
}


public function getLunchBreak(
    Carbon $date
): ?array
{
    $shift=$this->getShift($date);

    if(
        !$shift ||
        !$shift->break_start ||
        !$shift->break_end
    ){
        return null;
    }

    return [

        'start'=>Carbon::parse(
            $date->toDateString().' '.$shift->break_start
        ),

        'end'=>Carbon::parse(
            $date->toDateString().' '.$shift->break_end
        )

    ];
}


public function isOfficeOpen(
    Carbon $date
): bool
{
    if(!$this->isWorkingDay($date)){

        return false;

    }

    $shift=$this->getShift($date);

    if(!$shift){

        return false;

    }

    $start=Carbon::parse(
        $date->toDateString().' '.$shift->start_time
    );

    $end=Carbon::parse(
        $date->toDateString().' '.$shift->end_time
    );

    if($shift->end_time < $shift->start_time){

        $end->addDay();

    }

    if(!$date->betweenIncluded($start,$end)){

        return false;

    }

    $break=$this->getLunchBreak($date);

    if($break){

        if($date->betweenIncluded(
            $break['start'],
            $break['end']
        )){

            return false;

        }

    }

    return true;
}


public function remainingOfficeMinutes(
    Carbon $date
): int
{
    if(!$this->isOfficeOpen($date)){

        return 0;

    }

    $shift=$this->getShift($date);

    $end=Carbon::parse(
        $date->toDateString().' '.$shift->end_time
    );

    if($shift->end_time < $shift->start_time){
        $end->addDay();
    }

    $minutes=$date->diffInMinutes($end);

    $break=$this->getLunchBreak($date);

    if(
        $break &&
        $date->lt($break['start'])
    ){

        $minutes-=$break['start']
            ->diffInMinutes($break['end']);

    }

    return max($minutes,0);
}


public function workingMinutesBetween(
    Carbon $start,
    Carbon $end
): int
{
    if($start->gte($end)){
        return 0;
    }

    $minutes=0;

    $current=$start->copy();

    while($current->lt($end)){

        if($this->isOfficeOpen($current)){

            $minutes++;

        }

        $current->addMinute();

    }

    return $minutes;
}
################### Whenever an admin updates:Working Calendar, Working Schedule,Holiday Call below Method

public function refreshCache(): void
{
    $this->holidayCache=[];

    $this->scheduleCache=[];

    $this->shiftCache=[];

    $this->loadCache();
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