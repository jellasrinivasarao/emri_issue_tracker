<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationModule extends Model
{
    protected $table = 'mst_application_module';

    protected $primaryKey = 'module_id';

    public $timestamps = false;

    protected $fillable = [
        'application_id',
        'module_code',
        'module_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(
            Application::class,
            'application_id',
            'application_id'
        );
    }
}