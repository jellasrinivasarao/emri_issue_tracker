<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserRoleMappingController extends Controller
{
    public function index(): View
    {
        $mappings = DB::table('map_user_role as m')
            ->join('mst_user as u', 'm.user_id', '=', 'u.user_id')
            ->join('mst_role as r', 'm.role_id', '=', 'r.role_id')
            ->select(
                'm.user_role_id',
                'u.user_name',
                'u.login_id',
                'r.role_name',
                'm.is_active'
            )
            ->orderBy('u.user_name')
            ->get();

        return view('pages.user-role-mapping', [
            'title' => 'User–Role Mapping',
            'description' => 'Map users to roles and manage user role assignments.',
            'mappings' => $mappings,
        ]);
    }
}
