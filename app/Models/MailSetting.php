<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    use HasFactory;

    protected $table = 'mst_mail_setting';
    protected $primaryKey = 'mail_setting_id';

    protected $fillable = [
        'name',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'from_address',
        'from_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
