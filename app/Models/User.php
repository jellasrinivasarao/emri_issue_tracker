<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
