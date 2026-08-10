<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueAssignment extends Model
{
    protected $table = 'issue_assignments';

    protected $primaryKey = 'issue_assignment_id';

    public $timestamps = false;

    protected $fillable = [
        'issue_id',
        
        'assigned_from_organisation_id',
        'assigned_to_organisation_id',

        'assigned_from_group_id',
        'assigned_to_group_id',

        'assigned_to_user_id',
        'assigned_to_role_id',

        'assignment_reason',
        'assigned_at',

        'assigned_by_user_id',

        'is_current',
    ];

    protected $casts = [
        'assignment_id' => 'integer',
        'issue_id' => 'integer',

        'assigned_from_organisation_id' => 'integer',
        'assigned_to_organisation_id' => 'integer',

        'assigned_from_group_id' => 'integer',
        'assigned_to_group_id' => 'integer',

        'assigned_to_user_id' => 'integer',
        'assigned_to_role_id' => 'integer',

        'assigned_by_user_id' => 'integer',

        'assigned_at' => 'datetime',

        'is_current' => 'boolean',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(
            Issue::class,
            'issue_id',
            'issue_id'
        );
    }

    public function routingRule(): BelongsTo
    {
        return $this->belongsTo(
            IssueRoutingRule::class,
            'routing_rule_id',
            'routing_rule_id'
        );
    }

    public function supportTeam(): BelongsTo
    {
        return $this->belongsTo(
            SupportTeam::class,
            'support_team_id',
            'support_team_id'
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by',
            'id'
        );
    }

    
}