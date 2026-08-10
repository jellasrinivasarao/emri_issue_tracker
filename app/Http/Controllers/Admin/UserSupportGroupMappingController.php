<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserSupportGroupMappingController extends Controller
{
    public function index(): View
    {
        $mappings = DB::table('map_user_support_group as m')
            ->join('mst_user as u', 'm.user_id', '=', 'u.user_id')
            ->join('mst_support_group as g', 'm.support_group_id', '=', 'g.support_group_id')
            ->select(
                'm.id as mapping_id',
                'u.user_name',
                'u.login_id',
                'g.support_group_name',
                'm.is_active'
            )
            ->orderBy('u.user_name')
            ->get();

        return view('pages.user-support-group-mapping', [
            'title' => 'User–Support Group Mapping',
            'description' => 'Manage user membership in support groups.',
            'mappings' => $mappings,
        ]);
    }
}
