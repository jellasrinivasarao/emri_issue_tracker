<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingSchedule extends Model
{
    use SoftDeletes;
    
    protected $table = 'mst_working_schedule';

    protected $primaryKey = 'schedule_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'calendar_id',
        'day_of_week',
        'schedule_name',
        'start_time',
        'end_time',
        'is_working_day',
        'is_24_hours',
        'sequence_no',
        'effective_from',
        'effective_to',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'calendar_id' => 'integer',
        'day_of_week' => 'integer',
        'sequence_no' => 'integer',
        'is_working_day' => 'boolean',
        'is_24_hours' => 'boolean',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(WorkingCalendar::class,'calendar_id','calendar_id');
    }

    public function getDayNameAttribute(): string
    {
        return match ((int) $this->day_of_week) {

            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',

            default => 'Unknown',
        };
    }
}