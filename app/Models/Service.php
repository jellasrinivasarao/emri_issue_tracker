<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $table = 'mst_service';

    protected $primaryKey = 'service_id';

    public $timestamps = false;

    protected $fillable = [
        'service_code',
        'short_code',
        'service_name',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'service_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'map_project_service',
            'service_id',
            'project_id'
        );
        #->wherePivot('is_active', 1)
    }

    public function slaPolicies()
    {
        return $this->hasMany(
            SlaPolicy::class,
            'service_id'
        );
    }
}