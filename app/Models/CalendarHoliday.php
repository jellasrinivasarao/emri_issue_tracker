<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarHoliday extends Model
{
    use SoftDeletes;
    
    protected $table = 'mst_calendar_holiday';

    protected $primaryKey = 'holiday_id';

    public $timestamps = false;

    protected $fillable = [
        'calendar_id',
        'holiday_date',
        'holiday_code',
        'holiday_name',
        'holiday_type',
        'description',
        'is_working_day_override',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'calendar_id' => 'integer',
        'holiday_date' => 'date',
        'is_working_day_override' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(WorkingCalendar::class,'calendar_id','calendar_id');
    }
}