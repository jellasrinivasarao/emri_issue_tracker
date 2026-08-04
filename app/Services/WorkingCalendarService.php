<?php

namespace App\Services;

use App\Models\WorkingCalendar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WorkingCalendarService
{
    public function create(array $data): WorkingCalendar
    {
        return DB::transaction(function () use ($data) {

            $data['created_by'] = Auth::id();

            $calendar = WorkingCalendar::create($data);

            return $calendar;
        });
    }

    public function update(WorkingCalendar $calendar,array $data): WorkingCalendar {

        return DB::transaction(function () use ($calendar, $data) {

            $data['updated_by'] = Auth::id();

            $calendar->update($data);

            return $calendar->refresh();
        });
    }

    public function delete(WorkingCalendar $calendar): void {

        DB::transaction(function () use ($calendar) {

            $calendar->update([
                'deleted_by' => Auth::id(),
            ]);

            $calendar->delete();
        });
    }

    public function activate(WorkingCalendar $calendar): void {

        $calendar->update([
            'is_active' => true,
            'updated_by' => Auth::id(),
        ]);
    }

    public function deactivate(WorkingCalendar $calendar): void {

        $calendar->update([
            'is_active' => false,
            'updated_by' => Auth::id(),
        ]);
    }
}