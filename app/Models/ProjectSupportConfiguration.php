<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}