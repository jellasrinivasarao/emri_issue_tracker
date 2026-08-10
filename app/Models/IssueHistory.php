<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueHistory extends Model
{
    protected $table = 't_issue_history';

    protected $primaryKey = 'history_id';

    public $timestamps = false;

    protected $fillable = [
        'issue_id',
        'action',
        'from_status',
        'to_status',
        'from_team_id',
        'to_team_id',
        'remarks',
        'performed_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}