<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingCalendar extends Model
{

    use SoftDeletes;
    
    protected $table = 'mst_working_calendar';

    protected $primaryKey = 'calendar_id';

    public $timestamps = false;

    protected $keyType = 'int';

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'calendar_code',
        'calendar_name',
        'description',
        'organisation_id',
        'timezone',
        'effective_from',
        'effective_to',
        'version_no',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'organisation_id' => 'integer',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'version_no' => 'integer',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
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
        return $this->hasMany(WorkingSchedule::class,'calendar_id','calendar_id');
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(CalendarHoliday::class,'calendar_id','calendar_id');
    }
    

    public function slaPolicies()
    {
        return $this->hasMany(
            SlaPolicy::class,
            'calendar_id'
        );
    }




}