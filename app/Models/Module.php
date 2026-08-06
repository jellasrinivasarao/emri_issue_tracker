<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'mst_module';

    protected $primaryKey = 'module_id';

    public $timestamps = false;

    protected $fillable = [
        'module_code',
        'module_name',
        'description',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}