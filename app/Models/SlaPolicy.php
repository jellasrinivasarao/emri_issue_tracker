<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaPolicy extends Model
{
    protected $table = 'cfg_sla_policy';

    protected $primaryKey = 'sla_policy_id';

    public $timestamps = false;

    protected $fillable = [
        'service_id',
        'project_id',
        'application_id',
        'priority_id',
        'support_level',
        'response_time_minutes',
        'resolution_time_minutes',
        'warning_percentage',
        'calendar_id',
        'is_active',
    ];

    protected $casts = [
        'warning_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(
            Project::class,
            'project_id',
            'project_id'
        );
    }

    public function application()
    {
        return $this->belongsTo(
            Application::class,
            'application_id',
            'application_id'
        );
    }

    public function service()
    {
        return $this->belongsTo(
            Service::class,
            'service_id',
            'service_id'
        );
    }

    public function priority()
    {
        return $this->belongsTo(
            Priority::class,
            'priority_id',
            'priority_id'
        );
    }

    public function calendar()
    {
        return $this->belongsTo(
            WorkingCalendar::class,
            'calendar_id',
            'calendar_id'
        );
    }
}