<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'txn_issue_status_history';

    protected $primaryKey = 'status_history_id';

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
        | Status Change
        |--------------------------------------------------------------------------
        */

        'from_status_id',

        'to_status_id',

        'from_status',

        'to_status',

        /*
        |--------------------------------------------------------------------------
        | Assignment / Routing
        |--------------------------------------------------------------------------
        */

        'assignment_id',

        'routing_rule_id',

        'support_config_id',

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        'changed_by',

        /*
        |--------------------------------------------------------------------------
        | Change Details
        |--------------------------------------------------------------------------
        */

        'change_type',

        'remarks',

        'change_reason',

        /*
        |--------------------------------------------------------------------------
        | Date
        |--------------------------------------------------------------------------
        */

        'changed_at',
    ];

    protected $casts = [

        'issue_id' =>
            'integer',

        'from_status_id' =>
            'integer',

        'to_status_id' =>
            'integer',

        'assignment_id' =>
            'integer',

        'routing_rule_id' =>
            'integer',

        'support_config_id' =>
            'integer',

        'changed_by' =>
            'integer',

        'changed_at' =>
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
    | From Status
    |--------------------------------------------------------------------------
    */

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(
            IssueStatus::class,
            'from_status_id',
            'status_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | To Status
    |--------------------------------------------------------------------------
    */

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(
            IssueStatus::class,
            'to_status_id',
            'status_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Assignment
    |--------------------------------------------------------------------------
    */

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(
            IssueAssignment::class,
            'assignment_id',
            'assignment_id'
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
    | Changed By
    |--------------------------------------------------------------------------
    */

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'changed_by',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForIssue(
        $query,
        int $issueId
    ) {
        return $query
            ->where(
                'issue_id',
                $issueId
            )
            ->latest(
                'changed_at'
            );
    }


    public function scopeRouting(
        $query
    ) {
        return $query->where(
            'change_type',
            'ROUTING'
        );
    }


    public function scopeManual(
        $query
    ) {
        return $query->where(
            'change_type',
            'MANUAL'
        );
    }


    public function scopeAutomatic(
        $query
    ) {
        return $query->where(
            'change_type',
            'AUTO'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public function isStatusChange(): bool
    {
        return
            $this->from_status !==
            $this->to_status;
    }
}