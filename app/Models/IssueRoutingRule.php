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
        'project_id',
        'state_id',
        'application_id',
        'vendor_id',
        'hoit_id',
    ];

    protected $casts = [
        'routing_level' => 'integer',
        'is_default'    => 'boolean',
        'is_active'     => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(
            ProjectSupportConfiguration::class,
            'support_config_id',
            'support_config_id'
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


    // Add these when corresponding models exist.

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function hoIt(): BelongsTo
    {
        return $this->belongsTo(HoIt::class, 'hoit_id');
    }
}