<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportGroup extends Model
{
    protected $table = 'mst_support_group';

    protected $primaryKey = 'support_group_id';

    public $timestamps = false;

    protected $fillable = [
        'organisation_id',
        'support_group_code',
        'support_group_name',
        'support_group_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(
            Organisation::class,
            'organisation_id',
            'organisation_id'
        );
    }
}