<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSupportConfiguration extends Model
{
    protected $table = 'mst_project_support_configuration';

    protected $primaryKey = 'support_configuration_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'project_id',
        'configuration_code',
        'configuration_name',
        'default_support_level',
        'default_team_type',
        'sla_hours',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sla_hours' => 'decimal:2',
    ];

    public function routingRules()
    {
        return $this->hasMany(
            IssueRoutingRule::class,
            'support_configuration_id',
            'support_configuration_id'
        );
    }
}