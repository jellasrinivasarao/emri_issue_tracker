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
        'routing_rule_id',
        'support_team_id',
        'assignment_level',
        'support_level',
        'assignment_type',
        'status',
        'assigned_at',
        'created_by',
        'remarks',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
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