give<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menuMap = Menu::all()->keyBy('route_name');
        $roles = Role::all()->keyBy('role_name');

        $assignments = [
            'Central Admin' => [
                'dashboard',
                'issues',
                'raise.issue',
                'reports',
                'administration',
                'role.dashboard',
                'central.admin',
                'state.admin',
                'ho.admin',
                'vendor.admin',
                'state.master',
                'vendor.master',
                'service.master',
                'project.master',
                'application.master',
                'module.master',
                'project.application.module.mapping',
                'support-group.master',
                'user.master',
                'role.master',
                'privilege.master',
                'user.role.mapping',
                'user.project.mapping',
                'user.support.group.mapping',
                'menu.master',
                'role.menu.mapping',
                'role.privilege.mapping',
                'working.hours',
                'holiday.calendar',
                'sla.configuration',
                'automatic.routing',
                'notification.configuration',
                'mail.configuration',
                'priority.configuration',
                'severity.configuration',
                'issue.category.configuration',
                'vendor.level2.mapping',
                'vendor.state.mapping',
                'active.inactive.status',
                'change.history',
                'user.activity.log',
                'system.audit.logs',
            ],
            'State Admin' => [
                'dashboard',
                'raise.issue',
                'role.dashboard',
                'role.issue.dashboard',
                'state.admin',
            ],
            'HO Admin' => [
                'dashboard',
                'issues',
                'role.dashboard',
                'role.issue.dashboard',
                'ho.admin',
            ],
            'Vendor Admin' => [
                'dashboard',
                'issues',
                'role.dashboard',
                'role.issue.dashboard',
                'vendor.admin',
            ],
        ];

        foreach ($assignments as $roleName => $routes) {
            if (! isset($roles[$roleName])) {
                continue;
            }

            $role = $roles[$roleName];
            foreach ($routes as $routeName) {
                if (! isset($menuMap[$routeName])) {
                    continue;
                }
                $role->menus()->syncWithoutDetaching([
                    $menuMap[$routeName]->menu_id => ['is_allowed' => true],
                ]);
            }
        }
    }
}
