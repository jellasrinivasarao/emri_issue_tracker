<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mst_working_schedule';

    protected $primaryKey = 'schedule_id';

    public $incrementing = true;

    protected $keyType = 'int';

    const DELETED_AT = 'deleted_at';

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
        'break_start',
        'break_end',
        'shift_no',
        'shift_name',
    ];

    protected $casts = [
        'calendar_id'     => 'integer',
        'is_working_day'  => 'boolean',
        'is_24_hours'     => 'boolean',
        'sequence_no'     => 'integer',
        'is_active'       => 'boolean',
        'shift_no'        => 'integer',
        'effective_from'  => 'date',
        'effective_to'    => 'date',
        'deleted_at'      => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];              

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(WorkingCalendar::class,'calendar_id','calendar_id');
    }
}