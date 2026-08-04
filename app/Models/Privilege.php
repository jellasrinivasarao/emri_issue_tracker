<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    protected $table = 'mst_privilege';
    protected $primaryKey = 'privilege_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'privilege_code',
        'privilege_name',
        'module_name',
        'description',
    ];
}
