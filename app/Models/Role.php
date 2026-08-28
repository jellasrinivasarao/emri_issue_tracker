<?php

namespace App\Models;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const CENTRAL_ADMIN_ID = 1;
    public const STATE_ADMIN_ID = 2;
    public const VENDOR_ADMIN_ID = 3;
    public const HO_ADMIN_ID = 4;
    public const HO_IT_ID = 5;
    public const VENDOR_IT_ID = 6;
    public const STATE_IT_ID = 7;

    protected $table = 'mst_role';
    protected $primaryKey = 'role_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'role_code',
        'role_name',
        'role_category',
        'description',
        'is_system_role',
    ];

    public function menus()
    {
        return $this->belongsToMany(
            Menu::class,
            'map_role_privilege',
            'role_id',
            'menu_id'
        )->withPivot('is_allowed', 'privilege_id');
    }
}
