<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $table = 'mst_project';

    protected $primaryKey = 'project_id';

    public $timestamps = false;

    protected $fillable = [
        'state_id',
        'project_code',
        'short_code',
        'project_name',
        'project_description',
        'start_date',
        'end_date',
        'project_status',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(
            State::class,
            'state_id',
            'state_id'
        );
    }

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(
            Application::class,
            'map_project_application',
            'project_id',
            'application_id'
        );
        #->wherePivot('is_active', 1);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'map_project_service',
            'project_id',
            'service_id'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'map_user_project',
            'project_id',
            'user_id'
        )->wherePivot('is_active', 1);
    }

    public function routingRules()
    {
        return $this->hasMany(
            IssueRouting::class,
            'project_id',
            'project_id'
        );
    }

    public function slaPolicies()
    {
        return $this->hasMany(
            SlaPolicy::class,
            'project_id',
            'project_id'
        );
    }
}