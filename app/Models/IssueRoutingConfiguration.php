<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueRoutingConfiguration extends Model
{
    protected $table = 'mst_issue_routing_configuration';

    protected $primaryKey = 'configuration_id';

    public $timestamps = true;

    protected $fillable = [
        'configuration_code',
        'configuration_name',
        'routing_level',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];
}