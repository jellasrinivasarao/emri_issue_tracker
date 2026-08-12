<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueAssignment extends Model
{
    use HasFactory;

    protected $table = 'txn_issue_assignment';

    protected $primaryKey = 'assignment_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Issue
        |--------------------------------------------------------------------------
        */

        'issue_id',

        /*
        |--------------------------------------------------------------------------
        | Routing
        |--------------------------------------------------------------------------
        */

        'routing_rule_id',

        'support_config_id',

        /*
        |--------------------------------------------------------------------------
        | Assignment
        |--------------------------------------------------------------------------
        */

        'support_team_id',

        'assigned_user_id',

        'assignment_level',

        'assignment_type',

        'status',

        /*
        |--------------------------------------------------------------------------
        | Assignment Time
        |--------------------------------------------------------------------------
        */

        'assigned_at',

        'accepted_at',

        'started_at',

        'completed_at',

        /*
        |--------------------------------------------------------------------------
        | Assignment Details
        |--------------------------------------------------------------------------
        */

        'remarks',

        'assignment_reason',

        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        'assigned_by',
    ];

    protected $casts = [

        'issue_id' =>
            'integer',

        'routing_rule_id' =>
            'integer',

        'support_config_id' =>
            'integer',

        'support_team_id' =>
            'integer',

        'assigned_user_id' =>
            'integer',

        'assignment_level' =>
            'integer',

        'assigned_by' =>
            'integer',

        'assigned_at' =>
            'datetime',

        'accepted_at' =>
            'datetime',

        'started_at' =>
            'datetime',

        'completed_at' =>
            'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Issue
    |--------------------------------------------------------------------------
    */

    public function issue(): BelongsTo
    {
        return $this->belongsTo(
            Issue::class,
            'issue_id',
            'issue_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Routing Rule
    |--------------------------------------------------------------------------
    */

    public function routingRule(): BelongsTo
    {
        return $this->belongsTo(
            IssueRoutingRule::class,
            'routing_rule_id',
            'routing_rule_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Support Configuration
    |--------------------------------------------------------------------------
    */

    public function supportConfiguration(): BelongsTo
    {
        return $this->belongsTo(
            ProjectSupportConfiguration::class,
            'support_config_id',
            'support_config_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Support Team
    |--------------------------------------------------------------------------
    */

    public function supportTeam(): BelongsTo
    {
        return $this->belongsTo(
            SupportTeam::class,
            'support_team_id',
            'support_team_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Assigned User
    |--------------------------------------------------------------------------
    */

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_user_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Assigned By
    |--------------------------------------------------------------------------
    */

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_by',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scope - Active Assignment
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where(
            'status',
            'ASSIGNED'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scope - HO IT
    |--------------------------------------------------------------------------
    */

    public function scopeHoIt($query)
    {
        return $query->where(
            'assignment_level',
            1
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scope - Vendor
    |--------------------------------------------------------------------------
    */

    public function scopeVendor($query)
    {
        return $query->where(
            'assignment_level',
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scope - Automatic
    |--------------------------------------------------------------------------
    */

    public function scopeAutomatic($query)
    {
        return $query->where(
            'assignment_type',
            'AUTO'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scope - Manual
    |--------------------------------------------------------------------------
    */

    public function scopeManual($query)
    {
        return $query->where(
            'assignment_type',
            'MANUAL'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isHoIt(): bool
    {
        return $this->assignment_level === 1;
    }


    public function isVendor(): bool
    {
        return $this->assignment_level === 2;
    }


    public function isAutomatic(): bool
    {
        return $this->assignment_type === 'AUTO';
    }


    public function isActive(): bool
    {
        return $this->status === 'ASSIGNED';
    }
}