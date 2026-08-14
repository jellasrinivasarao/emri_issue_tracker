<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Menu;
use App\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mst_user';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Disable default timestamps (created_at, updated_at) - table uses different columns.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_code',
        'user_name',
        'login_id',
        'official_email',
        'mobile_number',
        'organisation_id',
        'user_status',
        'last_login_at',
        'password_changed_at',
        'password_reset_otp',
        'password_reset_otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'password_reset_otp_expires_at' => 'datetime',
    ];

    /**
     * Return the password for authentication (Laravel expects `getAuthPassword`).
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the e-mail address where password reset links are sent.
     */
    public function getEmailForPasswordReset(): ?string
    {
        return $this->official_email;
    }

    /**
     * Get the e-mail address where mail notifications are sent.
     */
    public function routeNotificationForMail($notification = null): ?string
    {
        return $this->official_email;
    }

    public function getNameAttribute(): ?string
    {
        return $this->user_name;
    }

    public function role(): BelongsTo
    {
        // If mst_user has a direct role_id column, use this relation.
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'map_user_role',
            'user_id',
            'role_id'
        );
    }

    public function getRoleNamesAttribute(): string
    {
        return $this->roles->pluck('role_name')->join(', ');
    }

    public function getMenusAttribute(): Collection
    {
        if (! $this->relationLoaded('roles')) {
            $this->load('roles.menus');
        }

        return $this->roles
            ->flatMap(fn(Role $role) => $role->menus)
            ->filter(fn($menu) => $menu->pivot->is_allowed && $menu->is_active)
            ->unique('menu_id')
            ->sortBy('display_order')
            ->values();
    }

    public function hasMenuAccess(string $routeName): bool
    {
        return $this->menus->contains(function ($menu) use ($routeName) {
            return strtolower($menu->route_name) === strtolower($routeName);
        });
    }

    public function hasPrivilege(int $menuId, string $privilegeCode): bool
    {
        if (! $this->relationLoaded('roles')) {
            $this->load('roles.menus');
        }

        $privilegeCode = strtolower($privilegeCode);

        return $this->roles->flatMap(function (Role $role) use ($menuId) {
            return $role->menus->filter(fn($menu) => (int) $menu->menu_id === (int) $menuId)->map(fn($menu) => $menu->pivot);
        })->contains(function ($pivot) use ($privilegeCode) {
            $code = DB::table('mst_privilege')->where('privilege_id', $pivot->privilege_id)->value('privilege_code');
            return $pivot->is_allowed && $code && strtolower($code) === $privilegeCode;
        });
    }

    public function hasPrivilegeOnRoute(string $routeName, string $privilegeCode): bool
    {
        $this->loadMissing('roles');

        $roleIds = $this->roles->pluck('role_id')->toArray();
        if (empty($roleIds)) {
            return false;
        }

        return DB::table('map_role_privilege as m')
            ->join('mst_menu as u', 'm.menu_id', '=', 'u.menu_id')
            ->join('mst_privilege as p', 'm.privilege_id', '=', 'p.privilege_id')
            ->whereIn('m.role_id', $roleIds)
            ->where('u.route_name', $routeName)
            ->where(DB::raw('LOWER(p.privilege_code)'), strtolower($privilegeCode))
            ->where('m.is_allowed', 1)
            ->exists();
    }

    public function getDefaultSectionRouteAttribute(): string
    {
        $priorityRoutes = [
            'central.admin',
            'state.admin',
            'ho.admin',
            'vendor.admin',
        ];

        foreach ($priorityRoutes as $routeName) {
            if ($this->hasMenuAccess($routeName) && Route::has($routeName)) {
                return route($routeName);
            }
        }

        return route('dashboard');
    }

    public function hasRole(string $roleName): bool
    {
        $this->loadMissing('roles');

        return $this->roles->contains(function (Role $role) use ($roleName) {
            return strtolower($role->role_name) === strtolower($roleName);
        });
    }
}
