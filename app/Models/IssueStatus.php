<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueStatus extends Model
{
    protected $table = 'mst_issue_status';

    protected $primaryKey = 'status_id';

    public $timestamps = false;

    protected $fillable = [
        'status_code',
        'status_name',
        'status_category',
        'is_closed_status',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_closed_status' => 'boolean',
        'is_active' => 'boolean',
    ];
}