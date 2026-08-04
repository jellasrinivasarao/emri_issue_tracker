<?php

namespace App\Services;

use App\Models\WorkingSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkingScheduleService
{
    public function save(array $data): WorkingSchedule {

        return DB::transaction(function () use ($data) {

            $data['created_by'] = Auth::id();

            return WorkingSchedule::create($data);
        });
    }

    public function update(WorkingSchedule $schedule,array $data): WorkingSchedule {

        $data['updated_by'] = Auth::id();

        $schedule->update($data);

        return $schedule->refresh();
    }

    public function delete(WorkingSchedule $schedule): void {

        $schedule->update([
            'deleted_by' => Auth::id(),
        ]);

        $schedule->delete();
    }
}