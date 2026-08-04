<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingSchedule extends Model
{
    protected $table = 'mst_working_schedule';

    protected $primaryKey = 'schedule_id';

    public $timestamps = false;

    protected $fillable = [
        'calendar_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_working_day',
    ];

    protected $casts = [
        'is_working_day' => 'boolean',
    ];

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(WorkingCalendar::class,'calendar_id','calendar_id');
    }
}