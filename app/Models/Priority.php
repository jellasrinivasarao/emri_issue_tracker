<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    protected $table = 'mst_priority';

    protected $primaryKey = 'priority_id';

    public $timestamps = false;

    protected $fillable = [
        'priority_code',
        'priority_name',
        'priority_level',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'priority_level' => 'integer',
        'is_active' => 'boolean',
    ];
}