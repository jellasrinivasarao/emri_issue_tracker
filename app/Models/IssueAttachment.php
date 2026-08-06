<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueAttachment extends Model
{
    protected $table = 'txn_issue_attachment';

    protected $primaryKey = 'attachment_id';

    protected $fillable = [
        'issue_id',
        'user_id',
        'original_file_name',
        'stored_file_name',
        'file_path',
        'file_size',
        'file_type',
        'uploaded_at',
        'is_active',
    ];
}