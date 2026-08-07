<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    protected $table = 'txn_issue';

    protected $primaryKey = 'issue_id';

    public $timestamps = false;
    
    protected $keyType = 'int';

    protected $fillable = [
    'issue_number',
    'service_id',
    'project_id',
    'application_id',
    'module_id',
    'issue_category_id',
    'priority_id',
    'status_id',
    'issue_title',
    'issue_description',
    'raised_by_user_id',
    'raised_at',
    'current_owner_organisation_id',
    'current_owner_group_id',
    'current_owner_user_id',
    'current_owner_role_id',
    'resolution_summary',
    'resolved_by',
    'resolved_at',
    'closed_at',
    'reopened_count',

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


    public function state()
    {
        return $this->belongsTo(State::class);
    }
    
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

    public function module()
    {
        return $this->belongsTo(
            Module::class,
            'module_id',
            'module_id'
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




    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }



     /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'Closed');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereHas('priority', function ($q) {
            $q->where('priority_name', 'High');
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment) {
            return null;
        }

        return asset('uploads/issues/' . $this->attachment);
    }

    public function getOccurredOnAttribute()
    {
        if (!$this->occurred_date) {
            return null;
        }

        return $this->occurred_date->format('d-m-Y') .
            ' ' .
            $this->occurred_time;
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {

            'Open' => 'success',

            'Pending' => 'warning',

            'In Progress' => 'primary',

            'Resolved' => 'info',

            'Closed' => 'secondary',

            default => 'dark',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */
    
     public function isOpen()
    {
        return $this->status === 'Open';
    }

    public function isClosed()
    {
        return $this->status === 'Closed';
    }

    public function hasAttachment()
    {
        return !empty($this->attachment);
    }

    public function priorityColor()
    {
        return match (strtolower(optional($this->priority)->priority_name)) {

            'critical' => '#dc2626',

            'high' => '#ea580c',

            'medium' => '#ca8a04',

            'low' => '#16a34a',

            default => '#6b7280',

        };
    }
}