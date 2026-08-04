<?php

namespace App\Models;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
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
            'map_role_menu',
            'role_id',
            'menu_id'
        )->withPivot('is_allowed');
    }
}
