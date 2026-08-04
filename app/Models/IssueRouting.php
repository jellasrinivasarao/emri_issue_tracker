<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueRouting extends Model
{
    protected $table = 'cfg_issue_routing';

    protected $primaryKey = 'routing_id';

    public $timestamps = false;

    protected $fillable = [
        'state_id',
        'service_id',
        'project_id',
        'application_id',
        'module_id',
        'ho_support_group_id',
        'vendor_support_group_id',
        'ho_calendar_id',
        'off_hours_routing_enabled',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    
    protected $casts = [
        'off_hours_routing_enabled' => 'boolean',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
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

    public function hoSupportGroup()
    {
        return $this->belongsTo(
            SupportGroup::class,
            'ho_support_group_id',
            'support_group_id'
        );
    }
    
    public function vendorSupportGroup()
    {
        return $this->belongsTo(
            SupportGroup::class,
            'vendor_support_group_id',
            'support_group_id'
        );
    }

    public function calendar()
    {
        return $this->belongsTo(
            WorkingCalendar::class,
            'ho_calendar_id',
            'calendar_id'
        );
    }
}