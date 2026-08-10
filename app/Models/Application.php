<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $table = 'mst_application';

    protected $primaryKey = 'application_id';

    public $timestamps = false;

    protected $fillable = [
        'application_code',
        'application_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'map_project_application',
            'application_id',
            'project_id'
        );

        #->wherePivot('is_active', 1);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(
            ApplicationModule::class,
            'application_id',
            'application_id'
        );
    }

    public function slaPolicies()
    {
        return $this->hasMany(
            SlaPolicy::class,
            'application_id'
        );
    }

}