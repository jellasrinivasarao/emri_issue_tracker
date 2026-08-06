<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueSla extends Model
{
    protected $table = 'txn_issue_sla';

    protected $primaryKey = 'issue_sla_id';

    public $timestamps = false;

    protected $fillable = [
        'issue_id',
        'sla_policy_id',
        'sla_policy_name',
        'assigned_to_organisation_id',
        'assigned_to_group_id',
        'assigned_to_user_id',
        'sla_start_at',
        'response_due_at',
        'resolution_due_at',
        'response_breached_at',
        'resolution_breached_at',
        'total_paused_minutes',
        'is_active',
    ];

    protected $casts = [
        'issue_id' => 'integer',
        'sla_policy_id' => 'integer',
        'assigned_to_organisation_id' => 'integer',
        'assigned_to_group_id' => 'integer',
        'assigned_to_user_id' => 'integer',

        'sla_start_at' => 'datetime',
        'response_due_at' => 'datetime',
        'resolution_due_at' => 'datetime',
        'response_breached_at' => 'datetime',
        'resolution_breached_at' => 'datetime',

        'total_paused_minutes' => 'integer',
        'is_active' => 'boolean',
    ];
}