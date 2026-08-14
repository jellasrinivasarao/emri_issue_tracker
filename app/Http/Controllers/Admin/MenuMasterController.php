<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\View\View;

class MenuMasterController extends Controller
{
    public function index(): View
    {
        $menus = Menu::query()
            ->select('menu_id', 'display_name', 'route_name', 'uri', 'parent_menu_id', 'icon', 'is_active', 'display_order')
            ->orderBy('display_order')
            ->orderBy('display_name')
            ->get();

        return view('pages.menu-master', [
            'title' => 'Menu Master',
            'description' => 'Manage menu items, route access, and sidebar navigation entries.',
            'menus' => $menus,
        ]);
    }
}
