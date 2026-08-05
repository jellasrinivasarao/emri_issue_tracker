<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueAssignment extends Model
{
    protected $table = 't_issue_assignment';

    protected $primaryKey = 'assignment_id';

    public $timestamps = false;

    protected $fillable = [
        'issue_id',
        'support_level',
        'support_team_id',
        'assigned_to',
        'assignment_type',
        'assigned_at',
        'released_at',
        'remarks',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'released_at' => 'datetime',
        'created_at' => 'datetime',
        'assignment_level' => 'integer',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}