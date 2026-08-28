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
        )->wherePivot('is_active', 1);
    }

    public function getRoleNamesAttribute(): string
    {
        return $this->roles->pluck('role_name')->join(', ');
    }

    public function getMenusAttribute(): Collection
    {
        if (! (bool) $this->is_active) {
            return collect();
        }

        $this->loadMissing('roles');

        $roleIds = $this->roles->pluck('role_id')->all();
        if (empty($roleIds)) {
            return collect();
        }

        $query = DB::table('map_role_privilege as m')
            ->join('mst_role as role', 'm.role_id', '=', 'role.role_id')
            ->where('m.is_allowed', 1)
            ->whereIn('m.role_id', $roleIds);

        $this->applyScopedRoleConditions($query, $roleIds);

        $menuIds = $query->pluck('m.menu_id')->unique();

        return Menu::whereIn('menu_id', $menuIds)
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->get()
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
        return $this->resolvePrivilege($menuId, null, $privilegeCode);
    }

    public function hasPrivilegeOnRoute(string $routeName, string $privilegeCode): bool
    {
        return $this->resolvePrivilege(null, $routeName, $privilegeCode);
    }

    private function resolvePrivilege(?int $menuId, ?string $routeName, string $privilegeCode): bool
    {
        if (! (bool) $this->is_active) {
            return false;
        }

        $this->loadMissing('roles');

        $roleIds = $this->roles->pluck('role_id')->map(fn ($id) => (int) $id)->all();
        if (empty($roleIds)) {
            return false;
        }

        $query = DB::table('map_role_privilege as m')
            ->join('mst_menu as u', 'm.menu_id', '=', 'u.menu_id')
            ->join('mst_privilege as p', 'm.privilege_id', '=', 'p.privilege_id')
            ->join('mst_role as role', 'm.role_id', '=', 'role.role_id')
            ->whereIn('m.role_id', $roleIds)
            ->where('u.is_active', 1)
            ->where('p.is_active', 1)
            ->where(DB::raw('LOWER(p.privilege_code)'), strtolower($privilegeCode))
            ->where('m.is_allowed', 1);

        if ($menuId !== null) {
            $query->where('m.menu_id', $menuId);
        }
        if ($routeName !== null) {
            $query->where('u.route_name', $routeName);
        }

        $this->applyScopedRoleConditions($query, $roleIds);

        return $query->exists();
    }

    private function applyScopedRoleConditions($query, array $roleIds): void
    {
        $roleNames = DB::table('mst_role')
            ->whereIn('role_id', $roleIds)
            ->pluck('role_name', 'role_id');

        $stateRoleIds = $roleNames
            ->filter(fn ($name) => strtolower(trim(preg_replace('/\s+/', ' ', (string) $name))) === 'state it')
            ->keys()
            ->all();
        $vendorRoleIds = $roleNames
            ->filter(fn ($name) => strtolower(trim(preg_replace('/\s+/', ' ', (string) $name))) === 'vendor it')
            ->keys()
            ->all();
        $globalRoleIds = array_values(array_diff($roleIds, $stateRoleIds, $vendorRoleIds));
        $stateIds = collect(preg_split('/\s*,\s*/', (string) ($this->state_id ?? ''), -1, PREG_SPLIT_NO_EMPTY))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $query->where(function ($scope) use ($globalRoleIds, $stateRoleIds, $vendorRoleIds, $stateIds) {
            if (! empty($globalRoleIds)) {
                $scope->whereIn('m.role_id', $globalRoleIds);
            }
            if (! empty($stateRoleIds) && ! empty($stateIds)) {
                $scope->orWhere(function ($state) use ($stateRoleIds, $stateIds) {
                    $state->whereIn('m.role_id', $stateRoleIds)->whereIn('m.state_id', $stateIds);
                });
            }
            if (! empty($vendorRoleIds) && ! empty($this->vendor_id)) {
                $scope->orWhere(function ($vendor) use ($vendorRoleIds) {
                    $vendor->whereIn('m.role_id', $vendorRoleIds)->where('m.vendor_id', $this->vendor_id);
                });
            }
        });
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

    public function hasRoleId(int $roleId): bool
    {
        if ((int) ($this->role_id ?? 0) === $roleId) {
            return true;
        }

        return $this->roles()->where('mst_role.role_id', $roleId)->exists();
    }

    public function hasAnyRoleId(array $roleIds): bool
    {
        $roleIds = array_values(array_filter(array_map('intval', $roleIds)));
        if (empty($roleIds)) {
            return false;
        }

        if (in_array((int) ($this->role_id ?? 0), $roleIds, true)) {
            return true;
        }

        return $this->roles()->whereIn('mst_role.role_id', $roleIds)->exists();
    }

    public function roleIds(): array
    {
        $roleIds = $this->roles()
            ->pluck('mst_role.role_id')
            ->map(fn ($roleId) => (int) $roleId)
            ->all();

        if (! empty($this->role_id)) {
            $roleIds[] = (int) $this->role_id;
        }

        return array_values(array_unique(array_filter($roleIds)));
    }
}
