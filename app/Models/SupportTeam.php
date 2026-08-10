<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTeam extends Model
{
    protected $table = 'mst_support_team';

    protected $primaryKey = 'support_team_id';

    protected $fillable = [
        'team_code',
        'team_name',
        'team_type',
        'support_level',
        'email',
        'phone',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function routingRules()
    {
        return $this->hasMany(
            IssueRoutingRule::class,
            'support_team_id',
            'support_team_id'
        );
    }
}