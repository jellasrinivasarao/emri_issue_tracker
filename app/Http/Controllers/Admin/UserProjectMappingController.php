<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserProjectMappingController extends Controller
{
    public function index(): View
    {
        $mappings = DB::table('map_user_project as m')
            ->join('mst_user as u', 'm.user_id', '=', 'u.user_id')
            ->join('mst_project as p', 'm.project_id', '=', 'p.project_id')
            ->select(
                'm.id as mapping_id',
                'u.user_name',
                'u.login_id',
                'p.project_name',
                'p.project_code',
                'm.is_active'
            )
            ->orderBy('u.user_name')
            ->get();

        return view('pages.user-project-mapping', [
            'title' => 'User–Project Mapping',
            'description' => 'Manage user access to projects and project assignments.',
            'mappings' => $mappings,
        ]);
    }
}
