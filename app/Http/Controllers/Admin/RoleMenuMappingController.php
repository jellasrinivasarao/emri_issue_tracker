<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoleMenuMappingController extends Controller
{
    public function index(): View
    {
        // Derive menu-level access from role+privilege mappings (map_role_privilege)
        $mappings = DB::table('map_role_privilege as m')
            ->join('mst_role as r', 'm.role_id', '=', 'r.role_id')
            ->join('mst_menu as u', 'm.menu_id', '=', 'u.menu_id')
            ->select(
                'm.role_privilege_id',
                'r.role_name',
                'u.display_name as menu_name',
                'u.route_name',
                'u.uri',
                'm.is_allowed'
            )
            ->groupBy('r.role_name', 'u.menu_id', 'u.display_name', 'u.route_name', 'u.uri', 'm.is_allowed', 'm.role_privilege_id')
            ->orderBy('r.role_name')
            ->orderBy('u.display_name')
            ->get();

        return view('pages.role-menu-mapping', [
            'title' => 'Role–Menu Mapping',
            'description' => 'Manage role permissions and menu access mapping.',
            'mappings' => $mappings,
        ]);
    }
}
