<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class State extends Model
{
    protected $table = 'mst_state';

    protected $primaryKey = 'state_id';

    public $timestamps = false;

    protected $fillable = [
        'organisation_id',
        'state_code',
        'state_name',
        'state_short_name',
        'is_active',
    ];

    protected $casts = [
        'state_id' => 'integer',
        'organisation_id' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(
            Organisation::class,
            'organisation_id',
            'organisation_id'
        );
    }

    public function projects(): HasMany
    {
        return $this->hasMany(
            Project::class,
            'state_id',
            'state_id'
        );
    }
    
}