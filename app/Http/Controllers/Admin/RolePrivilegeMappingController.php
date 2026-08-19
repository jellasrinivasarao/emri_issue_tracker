<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RolePrivilegeBulkRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RolePrivilegeMappingController extends Controller
{
    public function index(): View
    {
        $isCentralAdmin = auth()->user()->hasRole('Central Admin');
        $currentRoleIds = auth()->user()->roles()->pluck('mst_role.role_id')->map(fn ($id) => (int) $id)->all();
        if (empty($currentRoleIds) && ! empty(auth()->user()->role_id)) {
            $currentRoleIds = [(int) auth()->user()->role_id];
        }

        $childRoleIds = $this->childRoleIds($currentRoleIds);
        if (! empty($childRoleIds)) {
            $roles = DB::table('mst_role')
                ->select('role_id', 'role_name')
                ->whereIn('role_id', $childRoleIds)
                ->orderBy('role_name')
                ->get();
        } else {
            $roles = collect();
        }

        $privileges = DB::table('mst_privilege')
            ->select('privilege_id', 'privilege_code', 'privilege_name', 'display_order', 'is_active')
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->orderBy('privilege_id')
            ->get();

        $allowedMenuIds = [];
    if (! $isCentralAdmin && ! empty($currentRoleIds)) {
            $allowedMenuIds = DB::table('map_role_privilege')
        ->whereIn('role_id', $currentRoleIds)
                ->where('is_allowed', 1)
                ->pluck('menu_id')
        ->unique()
        ->values()
                ->toArray();
        }

        $menusQuery = DB::table('mst_menu')
            ->select('menu_id', 'parent_menu_id', 'display_name as menu_name', 'route_name', 'uri', 'display_order', 'is_active')
            ->where('is_active', 1)
            ->where('route_name', '<>', 'organization.setup')
            ->orderBy('display_order');

        if (! $isCentralAdmin) {
            $menusQuery->where('route_name', '<>', 'role.privilege.mapping');
            if (empty($allowedMenuIds)) {
                $menusQuery->whereRaw('1 = 0');
            } else {
                $menusQuery->whereIn('menu_id', $allowedMenuIds);
            }
        }

        $menus = $menusQuery->get();

        // Build nested menu tree
        $menuTree = $this->buildMenuTree($menus);

        // If a role is selected, load existing mappings for that role
        $selectedRoleId = request()->query('role_id');
        if ($selectedRoleId && ! in_array((int) $selectedRoleId, $childRoleIds, true)) {
            abort(403);
        }
        $existing = [];
        $totalMenus = 0;
        $totalPrivileges = $privileges->count();

        if ($selectedRoleId) {
            $rows = DB::table('map_role_privilege')
                ->where('role_id', $selectedRoleId)
                ->get();

            foreach ($rows as $r) {
                $existing[$r->menu_id][$r->privilege_id] = (int) $r->is_allowed;
            }
            $totalMenus = collect($menus)->count();
        }

        return view('pages.role-privilege-mapping', [
            'title' => 'Role–Privilege Mapping',
            'description' => 'Map roles to privileges and control role-based actions.',
            'roles' => $roles,
            'privileges' => $privileges,
            'menuTree' => $menuTree,
            'existing' => $existing,
            'selectedRoleId' => $selectedRoleId,
            'totalMenus' => $totalMenus,
            'totalPrivileges' => $totalPrivileges,
        ]);
    }

    public function store(RolePrivilegeBulkRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $roleId = (int) $data['role_id'];
        $permissions = $data['permissions'] ?? [];
        $isCentralAdmin = auth()->user()->hasRole('Central Admin');
        $currentRoleIds = auth()->user()->roles()->pluck('mst_role.role_id')->map(fn ($id) => (int) $id)->all();
        if (empty($currentRoleIds) && ! empty(auth()->user()->role_id)) {
            $currentRoleIds = [(int) auth()->user()->role_id];
        }
        $childRoleIds = $this->childRoleIds($currentRoleIds);

        if (! in_array($roleId, $childRoleIds, true)) {
            abort(403);
        }

        if (! $isCentralAdmin) {
            $allowedMenuIds = DB::table('map_role_privilege')
                ->whereIn('role_id', $currentRoleIds)
                ->where('is_allowed', 1)
                ->pluck('menu_id')
                ->map(fn ($id) => (string) $id)
                ->unique()
                ->all();
            $submittedMenuIds = array_map('strval', array_keys($permissions));
            if (array_diff($submittedMenuIds, $allowedMenuIds)) {
                abort(403);
            }
        }

        try {
            DB::transaction(function () use ($roleId, $permissions) {
                // reset current role mappings to not allowed
                DB::table('map_role_privilege')
                    ->where('role_id', $roleId)
                    ->update(['is_allowed' => 0, 'updated_at' => now()]);

                $rows = [];
                foreach ($permissions as $menuId => $privs) {
                    foreach ($privs as $privilegeId => $val) {
                        $rows[] = [
                            'role_id' => $roleId,
                            'menu_id' => (int) $menuId,
                            'privilege_id' => (int) $privilegeId,
                            'is_allowed' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (! empty($rows)) {
                    // upsert checked permissions to allowed
                    DB::table('map_role_privilege')
                        ->upsert($rows, ['role_id', 'menu_id', 'privilege_id'], ['is_allowed', 'updated_at']);
                }
            });

            return redirect()->route('role.privilege.mapping', ['role_id' => $roleId])->with('success', 'Role privilege mappings updated.');
        } catch (\Throwable $exception) {
            Log::error('Role privilege mapping save failed.', [
                'role_id' => $roleId,
                'user_id' => auth()->id(),
                'exception' => $exception->getMessage(),
            ]);

            return redirect()->route('role.privilege.mapping', ['role_id' => $roleId])
                ->with('error', 'Unable to save permissions: ' . $exception->getMessage());
        }
    }

    /**
     * Build nested menu tree array from flat collection
     *
     * @param  \Illuminate\Support\Collection  $menus
     * @return array
     */
    private function buildMenuTree($menus)
    {
        $items = [];
        foreach ($menus as $menu) {
            $items[$menu->menu_id] = (array) $menu;
            $items[$menu->menu_id]['children'] = [];
        }

        $tree = [];
        foreach ($items as $id => &$item) {
            $parent = $item['parent_menu_id'];
            if ($parent && isset($items[$parent])) {
                $items[$parent]['children'][] = &$item;
            } else {
                $tree[] = &$item;
            }
        }

        return $tree;
    }

    private function childRoleIds(array $parentRoleIds): array
    {
        if (empty($parentRoleIds)) {
            return [];
        }

        if (Schema::hasTable('map_role_hierarchy')) {
            $hierarchyQuery = DB::table('map_role_hierarchy')
                ->whereIn('parent_role_id', $parentRoleIds);
            if (Schema::hasColumn('map_role_hierarchy', 'is_active')) {
                $hierarchyQuery->where('is_active', 1);
            }

            return $hierarchyQuery
                ->pluck('child_role_id')
                ->map(fn ($id) => (int) $id)
                ->reject(fn ($id) => in_array($id, $parentRoleIds, true))
                ->unique()
                ->values()
                ->all();
        }

        $parentNames = DB::table('mst_role')
            ->whereIn('role_id', $parentRoleIds)
            ->pluck('role_name');
        $childNames = $parentNames->map(fn ($roleName) => match (strtolower(trim($roleName))) {
            'state admin' => 'State IT',
            'vendor admin' => 'Vendor IT',
            'ho admin' => 'HO IT',
            default => null,
        })->filter()->values()->all();

        return DB::table('mst_role')
            ->whereIn('role_name', $childNames)
            ->pluck('role_id')
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => in_array($id, $parentRoleIds, true))
            ->unique()
            ->values()
            ->all();
    }

    public function update(RolePrivilegeMappingRequest $request, int $role_privilege_id): RedirectResponse
    {
        $data = $request->validated();
        $data['is_allowed'] = (int) $data['is_allowed'];
        $data['updated_at'] = now();

        DB::table('map_role_privilege')
            ->where('role_privilege_id', $role_privilege_id)
            ->update($data);

        return redirect()->route('role.privilege.mapping')->with('success', 'Role privilege mapping updated successfully.');
    }

    public function toggle(int $role_privilege_id): RedirectResponse
    {
        $mapping = DB::table('map_role_privilege')->where('role_privilege_id', $role_privilege_id)->first();
        if (! $mapping) {
            return redirect()->route('role.privilege.mapping')->with('error', 'Mapping not found.');
        }

        DB::table('map_role_privilege')
            ->where('role_privilege_id', $role_privilege_id)
            ->update([
                'is_allowed' => ! (int) $mapping->is_allowed,
                'updated_at' => now(),
            ]);

        return redirect()->route('role.privilege.mapping')->with('success', 'Role privilege mapping toggled successfully.');
    }
}
