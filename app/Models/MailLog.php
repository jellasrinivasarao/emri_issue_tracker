<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    use HasFactory;

    protected $table = 'mst_mail_log';
    protected $primaryKey = 'mail_log_id';

    protected $fillable = [
        'user_id',
        'to_address',
        'subject',
        'body',
        'status',
        'error_message',
        'mailer',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
