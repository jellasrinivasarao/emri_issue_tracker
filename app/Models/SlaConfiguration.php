<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SlaConfiguration extends Model
{
    use HasFactory;

    protected $table = 'mst_sla_configuration';

    protected $primaryKey = 'sla_configuration_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'sla_code',
        'sla_name',
        'description',
        'response_sla_hours',
        'resolution_sla_hours',
        'escalation_sla_hours',
        'support_level',
        'working_calendar_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'response_sla_hours' => 'decimal:2',
        'resolution_sla_hours' => 'decimal:2',
        'escalation_sla_hours' => 'decimal:2',
        'support_level' => 'integer',
        'working_calendar_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function workingCalendar(): BelongsTo
    {
        return $this->belongsTo(
            WorkingCalendar::class,
            'working_calendar_id',
            'working_calendar_id'
        );
    }

    public function getSupportLevelNameAttribute(): string
    {
        return match ($this->support_level) {
            1 => 'HO IT Level-1',
            2 => 'Vendor Level-2',
            default => 'Not Defined',
        };
    }
}