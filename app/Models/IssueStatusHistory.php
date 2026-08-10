<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueStatusHistory extends Model
{
    protected $table = 't_issue_status_history';

    protected $primaryKey = 'status_history_id';

    public $timestamps = false;

    protected $fillable = [
        'issue_id',
        'old_status',
        'new_status',
        'remarks',
        'changed_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}