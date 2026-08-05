<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $table = 'txn_issue';

    protected $primaryKey = 'issue_id';

    public $timestamps = false;

    protected $fillable = [
        'issue_number',
        'state_id',
        'service_id',
        'project_id',
        'application_id',
        'module_id',
        'issue_category_id',
        'priority_id',
        'status_id',
        'subject',
        'issue_description',
        'occurred_at',
        'affected_user_count',
        'operational_impact',
        'raised_by',
        'raised_at',
        'current_owner_organisation_id',
        'current_support_group_id',
        'current_owner_user_id',
        'routing_mode',
        'resolution_summary',
        'resolved_by',
        'resolved_at',
        'closure_confirmed_by',
        'closed_at',
        'reopened_count',
        'is_active',
        'issue_number',
        'project_id',
        'issue_category_id',
        'issue_type_id',
        'priority_id',
        'title',
        'description',
        'status',
        'support_level',
        'support_team_id',
        'routing_rule_id',
        'sla_hours',
        'sla_due_at',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'created_by',
        'updated_by',
    ];


    protected $casts = [
        'occurred_at' => 'datetime',
        'raised_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_active' => 'boolean',
        'sla_due_at' => 'datetime',
        'project_id' => 'integer',
        'support_config_id' => 'integer',
        'reported_by' => 'integer',
        'current_team_id' => 'integer',
        'current_assignee_id' => 'integer',
        'opened_at' => 'datetime',
        'assigned_at' => 'datetime',
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

    public function category()
    {
        return $this->belongsTo(
            IssueCategory::class,
            'issue_category_id',
            'issue_category_id'
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

    public function status()
    {
        return $this->belongsTo(
            IssueStatus::class,
            'status_id',
            'status_id'
        );
    }

    
    public function raisedBy()
    {
        return $this->belongsTo(
            User::class,
            'raised_by',
            'user_id'
        );
    }

    public function assignments()
    {
        return $this->hasMany(
            IssueAssignment::class,
            'issue_id',
            'issue_id'
        );
    }

    public function statusHistory()
    {
        return $this->hasMany(
            IssueStatusHistory::class,
            'issue_id',
            'issue_id'
        );
    }

    

    public function updates()
    {
        return $this->hasMany(
            IssueUpdate::class,
            'issue_id',
            'issue_id'
        );
    }

    public function attachments()
    {
        return $this->hasMany(
            IssueAttachment::class,
            'issue_id',
            'issue_id'
        );
    }

    public function sla()
    {
        return $this->hasOne(
            IssueSla::class,
            'issue_id',
            'issue_id'
        );
    }

    public function escalations()
    {
        return $this->hasMany(
            IssueEscalation::class,
            'issue_id',
            'issue_id'
        );
    }



    public function configuration()
    {
        return $this->belongsTo(
            ProjectSupportConfiguration::class,
            'support_config_id',
            'support_config_id'
        );
    }

    public function team()
    {
        return $this->belongsTo(
            SupportTeam::class,
            'current_team_id',
            'support_team_id'
        );
    }

    public function histories()
    {
        return $this->hasMany(
            IssueHistory::class,
            'issue_id',
            'issue_id'
        );
    }
}