<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    protected $table = 'mst_menu';
    protected $primaryKey = 'menu_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'display_name',
        'route_name',
        'uri',
        'parent_menu_id',
        'icon',
        'is_active',
        'display_order',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'map_role_privilege',
            'menu_id',
            'role_id'
        )->withPivot('is_allowed', 'privilege_id');
    }
}
