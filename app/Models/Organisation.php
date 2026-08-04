<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    protected $table = 'mst_organisation';

    protected $primaryKey = 'organisation_id';

    public $timestamps = false;

    protected $fillable = [
        'organisation_type_id',
        'organisation_code',
        'organisation_name',
        'description',
        'is_active',
         'organisation_type_id',
        'organisation_code',
        'organisation_name',
        'short_name',
        'email',
        'mobile',
        'address_line1',
        'address_line2',
        'city',
        'state_name',
        'country',
        'pincode',
        'is_active',
        'created_by',
        'updated_by',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function organisationType(): BelongsTo
    {
        return $this->belongsTo(
            OrganisationType::class,
            'organisation_type_id',
            'organisation_type_id'
        );
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(
            OrganisationType::class,
            'organisation_type_id',
            'organisation_type_id'
        );
    }

    public function states(): HasMany
    {
        return $this->hasMany(
            State::class,
            'organisation_id',
            'organisation_id'
        );
    }

    public function headOffices(): HasMany
    {
        return $this->hasMany(
            HeadOffice::class,
            'organisation_id',
            'organisation_id'
        );
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(
            Vendor::class,
            'organisation_id',
            'organisation_id'
        );
    }
    
    public function supportGroups(): HasMany
    {
        return $this->hasMany(
            SupportGroup::class,
            'organisation_id',
            'organisation_id'
        );
    }

    public function calendars(): HasMany
    {
        return $this->hasMany(
            WorkingCalendar::class,
            'organisation_id',
            'organisation_id'
        );
    }
}