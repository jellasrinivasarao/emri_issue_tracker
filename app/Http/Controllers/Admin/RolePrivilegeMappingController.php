<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RolePrivilegeBulkRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class RolePrivilegeMappingController extends Controller
{
    public function index(): View
    {
        // Load available roles, active menus and privileges
        $roles = DB::table('mst_role')
            ->select('role_id', 'role_name')
            ->orderBy('role_name')
            ->get();

        $privileges = DB::table('mst_privilege')
            ->select('privilege_id', 'privilege_code', 'privilege_name', 'display_order', 'is_active')
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->orderBy('privilege_id')
            ->get();

        $menus = DB::table('mst_menu')
            ->select('menu_id', 'parent_menu_id', 'display_name as menu_name', 'route_name', 'uri', 'display_order', 'is_active')
            ->where('is_active', 1)
            ->where('route_name', '<>', 'organization.setup')
            ->orderBy('display_order')
            ->get();

        // Build nested menu tree
        $menuTree = $this->buildMenuTree($menus);

        // If a role is selected, load existing mappings for that role
        $selectedRoleId = request()->query('role_id');
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
            return redirect()->route('role.privilege.mapping', ['role_id' => $roleId])->with('error', 'Unable to save permissions. Please try again.');
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
