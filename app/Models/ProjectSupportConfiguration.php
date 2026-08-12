<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectSupportConfiguration extends Model
{
    protected $table = 'mst_project_support_configuration';

    protected $primaryKey = 'support_config_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'project_id',
        'config_code',
        'config_name',
        'default_support_level',
        'default_team_type',
        'default_priority',
        'auto_routing_enabled',
        'sla_hours',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_support_level' => 'boolean',
        'default_priority' => 'integer',
        'auto_routing_enabled' => 'boolean',
        'sla_hours' => 'decimal:2',
    ];

    public function routingRules()
    {
        return $this->hasMany(
            IssueRoutingRule::class,
            'support_config_id',
            'support_config_id'
        );
    }

    public function slaConfiguration()
    {
        return $this->belongsTo(
            SlaConfiguration::class,
            'sla_configuration_id',
            'sla_configuration_id'
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



    public function activeRoutingRules(): HasMany
    {
        return $this->hasMany(
            IssueRoutingRule::class,
            'support_config_id',
            'support_config_id'
        )->where(
            'is_active',
            true
        );
    }


    public function scopeActive($query)
    {
        return $query->where(
            'is_active',
            true
        );
    }
    public function scopeAutoRoutingEnabled($query)
    {
        return $query
            ->where('is_active', true)
            ->where(
                'auto_routing_enabled',
                true
            );
    }

public function workingCalendar()
{
    return $this->belongsTo(
        WorkingCalendar::class,
        'working_calendar_id',
        'calendar_id'
    );
}


}