<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requirement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'requirement_no',
        'title',
        'description',
        'state_id',
        'project_id',
        'brd_raised_by',
        'ho_it_team',
        'received_at',
        'requested_to_vendor_at',
        'additional_details',
        'status',
        'man_days',
        'timeline',
        'delivery_status',
        'vendor_remarks',
        'assigned_vendor_id',
        'created_by',
        'updated_by',
    ];

        protected $casts = [
        'ho_it_team' => 'boolean',
        'received_at' => 'date',
        'requested_to_vendor_at' => 'date',
        'man_days' => 'decimal:2',
    ];


    public function state()
    {
        return $this->belongsTo(State::class);
    }


    public function project()
    {
        return $this->belongsTo(Project::class);
    }


    public function files()
    {
        return $this->hasMany(
            RequirementFile::class
        );
    }


    public function statusHistory()
    {
        return $this->hasMany(
            RequirementStatusHistory::class
        );
    }


    public function clarifications()
    {
        return $this->hasMany(
            Clarification::class
        );
    }

    public function openClarifications()
    {
        return $this->clarifications()
            ->where('status', 'Open');
    }


    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function vendor()
    {
        return $this->belongsTo(
            User::class,
            'assigned_vendor_id'
        );
    }
}