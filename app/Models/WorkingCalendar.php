<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class WorkingCalendar extends Model
{
    protected $table = 'mst_working_calendar';

    protected $primaryKey = 'calendar_id';

    public $timestamps = false;

    protected $fillable = [
        'calendar_code',
        'calendar_name',
        'organisation_id',
        'timezone',
        'is_active',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(
            Organisation::class,
            'organisation_id',
            'organisation_id'
        );
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(
            WorkingSchedule::class,
            'calendar_id',
            'calendar_id'
        );
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(
            CalendarHoliday::class,
            'calendar_id',
            'calendar_id'
        );
    }
    




}