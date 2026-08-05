<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueRoutingRule extends Model
{
    protected $table = 'mst_issue_routing_rule';

    protected $primaryKey = 'routing_rule_id';

    protected $fillable = [
        'rule_code',
        'rule_name',
        'project_id',
        'support_configuration_id',
        'issue_category_id',
        'issue_type_id',
        'priority_id',
        'support_level',
        'support_team_id',
        'sla_hours',
        'routing_priority',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sla_hours' => 'decimal:2',
        'routing_level' => 'integer',
        'is_default' => 'boolean',
    ];


    public function configuration()
    {
        return $this->belongsTo(
            ProjectSupportConfiguration::class,
            'support_configuration_id',
            'support_configuration_id'
        );
    }

    public function team()
    {
        return $this->belongsTo(
            SupportTeam::class,
            'support_team_id',
            'support_team_id'
        );
    }
}