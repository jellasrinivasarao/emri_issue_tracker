<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RolePrivilegeMappingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class RolePrivilegeMappingController extends Controller
{
    public function index(): View
    {
        $mappings = DB::table('map_role_privilege as m')
            ->join('mst_role as r', 'm.role_id', '=', 'r.role_id')
            ->join('mst_privilege as p', 'm.privilege_id', '=', 'p.privilege_id')
            ->select(
                'm.role_privilege_id',
                'm.role_id',
                'm.privilege_id',
                'r.role_name',
                'p.privilege_code',
                'p.privilege_name',
                'p.module_name',
                'm.is_allowed'
            )
            ->orderBy('r.role_name')
            ->orderBy('p.privilege_name')
            ->get();

        $roles = DB::table('mst_role')
            ->select('role_id', 'role_name')
            ->orderBy('role_name')
            ->get();

        $privileges = DB::table('mst_privilege')
            ->select('privilege_id', 'privilege_code', 'privilege_name')
            ->orderBy('privilege_name')
            ->get();

        return view('pages.role-privilege-mapping', [
            'title' => 'Role–Privilege Mapping',
            'description' => 'Map roles to privileges and control role-based actions.',
            'mappings' => $mappings,
            'roles' => $roles,
            'privileges' => $privileges,
        ]);
    }

    public function store(RolePrivilegeMappingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_allowed'] = (int) $data['is_allowed'];
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('map_role_privilege')->insert($data);

        return redirect()->route('role.privilege.mapping')->with('success', 'Role privilege mapping added successfully.');
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
