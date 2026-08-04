<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarHoliday extends Model
{
    protected $table = 'mst_calendar_holiday';

    protected $primaryKey = 'holiday_id';

    public $timestamps = false;

    protected $fillable = [
        'calendar_id',
        'holiday_date',
        'holiday_name',
        'is_active',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(WorkingCalendar::class,'calendar_id','calendar_id');
    }
}