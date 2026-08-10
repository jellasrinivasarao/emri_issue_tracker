<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectApplicationModuleMapping extends Model
{
    protected $table = 'map_project_application_module';

    protected $primaryKey = 'mapping_id';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'application_id',
        'module_id',
        'is_active',
        'created_at',
        'created_by',
        'update_at',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'application_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(ApplicationModule::class, 'module_id', 'module_id');
    }
}
