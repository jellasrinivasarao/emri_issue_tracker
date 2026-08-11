<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Issue extends Model
{
    use HasFactory;

    protected $table = 'txn_issue';

    protected $primaryKey = 'issue_id';

    public $incrementing = true;

    public $timestamps = false;

    protected $keyType = 'int';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'issue_number',

        'service_id',
        'project_id',
        'state_id',
        'support_config_id',
        'application_id',
        'module_id',
        'issue_category_id',
        'priority_id',
        'status_id',

        'issue_title',
        'issue_description',

        'raised_by_user_id',
        'raised_at',
        'occurred_at',

        'current_owner_organisation_id',
        'current_owner_group_id',
        'current_owner_user_id',
        'current_owner_role_id',

        'current_team_id',
        'current_assignee_id',

        'resolution_summary',
        'resolved_by',
        'resolved_at',
        'closed_at',

        'reopened_count',

        'is_active',
        'sla_due_at',

        'reported_by',

        'opened_at',
        'assigned_at',

        'created_by',
        'updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'issue_id' => 'integer',

        'service_id' => 'integer',
        'project_id' => 'integer',
        'state_id' => 'integer',
        'support_config_id' => 'integer',
        'application_id' => 'integer',
        'module_id' => 'integer',
        'issue_category_id' => 'integer',
        'priority_id' => 'integer',
        'status_id' => 'integer',

        'raised_by_user_id' => 'integer',

        'current_owner_organisation_id' => 'integer',
        'current_owner_group_id' => 'integer',
        'current_owner_user_id' => 'integer',
        'current_owner_role_id' => 'integer',

        'current_team_id' => 'integer',
        'current_assignee_id' => 'integer',

        'resolved_by' => 'integer',
        'reported_by' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',

        'reopened_count' => 'integer',

        'is_active' => 'boolean',

        'occurred_at' => 'datetime',
        'raised_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',

        'sla_due_at' => 'datetime',
        'opened_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function state(): BelongsTo
    {
        return $this->belongsTo(
            State::class,
            'state_id',
            'state_id'
        );
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(
            Service::class,
            'service_id',
            'service_id'
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class,
            'project_id',
            'project_id'
        );
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(
            Application::class,
            'application_id',
            'application_id'
        );
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(
            Module::class,
            'module_id',
            'module_id'
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            IssueCategory::class,
            'issue_category_id',
            'issue_category_id'
        );
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(
            Priority::class,
            'priority_id',
            'priority_id'
        );
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(
            IssueStatus::class,
            'status_id',
            'status_id'
        );
    }

    public function raisedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'raised_by_user_id',
            'user_id'
        );
    }

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(
            ProjectSupportConfiguration::class,
            'support_config_id',
            'support_config_id'
        );
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(
            SupportTeam::class,
            'current_team_id',
            'support_team_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by',
            'user_id'
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by',
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Has Many Relationships
    |--------------------------------------------------------------------------
    */

    public function assignments(): HasMany
    {
        return $this->hasMany(
            IssueAssignment::class,
            'issue_id',
            'issue_id'
        );
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(
            IssueStatusHistory::class,
            'issue_id',
            'issue_id'
        );
    }

    public function updates(): HasMany
    {
        return $this->hasMany(
            IssueUpdate::class,
            'issue_id',
            'issue_id'
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(
            IssueAttachment::class,
            'issue_id',
            'issue_id'
        );
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(
            IssueEscalation::class,
            'issue_id',
            'issue_id'
        );
    }

    public function histories(): HasMany
    {
        return $this->hasMany(
            IssueHistory::class,
            'issue_id',
            'issue_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Has One Relationships
    |--------------------------------------------------------------------------
    */

    public function sla(): HasOne
    {
        return $this->hasOne(
            IssueSla::class,
            'issue_id',
            'issue_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOpen($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('status_name', 'Open');
        });
    }

    public function scopeClosed($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('status_name', 'Closed');
        });
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

    public function getStatusBadgeAttribute(): string
    {
        return match (strtolower($this->status?->status_name ?? '')) {
            'open' => 'success',
            'pending' => 'warning',
            'in progress' => 'primary',
            'resolved' => 'info',
            'closed' => 'secondary',
            default => 'dark',
        };
    }

    public function getOccurredOnAttribute(): ?string
    {
        return $this->occurred_at?->format('d-m-Y H:i:s');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isOpen(): bool
    {
        return strtolower($this->status?->status_name ?? '') === 'open';
    }

    public function isClosed(): bool
    {
        return strtolower($this->status?->status_name ?? '') === 'closed';
    }

    public function hasAttachments(): bool
    {
        return $this->attachments()->exists();
    }

    public function priorityColor(): string
    {
        return match (strtolower($this->priority?->priority_name ?? '')) {
            'critical' => '#dc2626',
            'high' => '#ea580c',
            'medium' => '#ca8a04',
            'low' => '#16a34a',
            default => '#6b7280',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'issue_id';
    }
}