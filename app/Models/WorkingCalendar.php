<?php

namespace App\Models;

use App\Models\CalendarHoliday;
use App\Models\Organisation;
use App\Models\State;
use App\Models\WorkingSchedule;
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
        'state_id',
        'timezone',
        'is_active',
    ];

    protected $casts = [
        'organisation_id' => 'integer',
        'state_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(
            Organisation::class,
            'organisation_id',
            'organisation_id'
        );
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(
            State::class,
            'state_id',
            'state_id'
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

    public function slaConfigurations()
{
    return $this->hasMany(
        SlaConfiguration::class,
        'calendar_id',
        'calendar_id'
    );
}
    




}