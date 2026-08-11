<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueRoutingRule extends Model
{
    use HasFactory;

    protected $table = 'mst_issue_routing_rule';

    protected $primaryKey = 'routing_rule_id';

    public $timestamps = true;

    protected $fillable = [
        'support_config_id',
        'support_team_id',
        'rule_code',
        'rule_name',
        'issue_category',
        'issue_type',
        'priority',
        'routing_level',
        'is_default',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'routing_level' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

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
            'support_team_id',
            'support_team_id'
        );
    }
}