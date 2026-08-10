<?php

namespace App\Services;

use App\Models\CalendarHoliday;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarHolidayService
{
    public function create(array $data): CalendarHoliday {

        return DB::transaction(function () use ($data) {

            $data['created_by'] = Auth::id();

            return CalendarHoliday::create($data);
        });
    }

    public function update(CalendarHoliday $holiday,array $data): CalendarHoliday {

        $data['updated_by'] = Auth::id();

        $holiday->update($data);

        return $holiday->refresh();
    }

    public function delete(CalendarHoliday $holiday): void {

        $holiday->update([
            'deleted_by' => Auth::id(),
        ]);

        $holiday->delete();
    }
}