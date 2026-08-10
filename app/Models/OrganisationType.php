<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganisationType extends Model
{
    protected $table = 'mst_organisation_type';

    protected $primaryKey = 'organisation_type_id';

    public $timestamps = false;

    protected $fillable = [
        'organisation_type_code',
        'organisation_type_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organisations(): HasMany
    {
        return $this->hasMany(
            Organisation::class,
            'organisation_type_id',
            'organisation_type_id'
        );
    }
}