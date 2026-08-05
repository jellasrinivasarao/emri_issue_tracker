<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['display_name' => 'Dashboard', 'route_name' => 'dashboard', 'uri' => '/dashboard', 'display_order' => 1],
            ['display_name' => 'Issues', 'route_name' => 'issues', 'uri' => '/issues', 'display_order' => 2],
            ['display_name' => 'Raise Issue', 'route_name' => 'raise.issue', 'uri' => '/raise-issue', 'display_order' => 3],
            ['display_name' => 'Reports', 'route_name' => 'reports', 'uri' => '/reports', 'display_order' => 4],
            ['display_name' => 'Administration', 'route_name' => 'administration', 'uri' => '/administration', 'display_order' => 5],
            ['display_name' => 'Main Dashboard', 'route_name' => 'role.dashboard', 'uri' => '/role-dashboard', 'display_order' => 6],
            ['display_name' => 'State Admin', 'route_name' => 'state.admin', 'uri' => '/state-admin', 'display_order' => 7],
            ['display_name' => 'HO Admin', 'route_name' => 'ho.admin', 'uri' => '/ho-admin', 'display_order' => 8],
            ['display_name' => 'Vendor Admin', 'route_name' => 'vendor.admin', 'uri' => '/vendor-admin', 'display_order' => 9],
            ['display_name' => 'State Master', 'route_name' => 'state.master', 'uri' => '/state-master', 'display_order' => 10],
            ['display_name' => 'Vendor Master', 'route_name' => 'vendor.master', 'uri' => '/vendor-master', 'display_order' => 11],
            ['display_name' => 'Service Master', 'route_name' => 'service.master', 'uri' => '/service-master', 'display_order' => 12],
            ['display_name' => 'Project Master', 'route_name' => 'project.master', 'uri' => '/project-master', 'display_order' => 13],
            ['display_name' => 'Application Master', 'route_name' => 'application.master', 'uri' => '/application-master', 'display_order' => 14],
            ['display_name' => 'Module Master', 'route_name' => 'module.master', 'uri' => '/module-master', 'display_order' => 15],
            ['display_name' => 'Project Mappings', 'route_name' => 'project.application.module.mapping', 'uri' => '/project-application-module', 'display_order' => 16],
            ['display_name' => 'Support Group Master', 'route_name' => 'support-group.master', 'uri' => '/support-group-master', 'display_order' => 17],
            ['display_name' => 'User Master', 'route_name' => 'user.master', 'uri' => '/user-master', 'display_order' => 18],
            ['display_name' => 'Role Master', 'route_name' => 'role.master', 'uri' => '/role-master', 'display_order' => 19],
            ['display_name' => 'Privilege Master', 'route_name' => 'privilege.master', 'uri' => '/privilege-master', 'display_order' => 20],
            ['display_name' => 'User–Role Mapping', 'route_name' => 'user.role.mapping', 'uri' => '/user-role-mapping', 'display_order' => 21],
            ['display_name' => 'User–Project Mapping', 'route_name' => 'user.project.mapping', 'uri' => '/user-project-mapping', 'display_order' => 22],
            ['display_name' => 'User–Support Group Mapping', 'route_name' => 'user.support.group.mapping', 'uri' => '/user-support-group-mapping', 'display_order' => 23],
            ['display_name' => 'Menu Master', 'route_name' => 'menu.master', 'uri' => '/menu-master', 'display_order' => 24],
            ['display_name' => 'Role–Menu Mapping', 'route_name' => 'role.menu.mapping', 'uri' => '/role-menu-mapping', 'display_order' => 25],
            ['display_name' => 'Role–Privilege Mapping', 'route_name' => 'role.privilege.mapping', 'uri' => '/role-privilege-mapping', 'display_order' => 26],
            ['display_name' => 'Working Hours', 'route_name' => 'working.hours', 'uri' => '/working-hours', 'display_order' => 27],
            ['display_name' => 'Holiday Calendar', 'route_name' => 'holiday.calendar', 'uri' => '/holiday-calendar', 'display_order' => 28],
            ['display_name' => 'SLA Configuration', 'route_name' => 'sla.configuration', 'uri' => '/sla-configuration', 'display_order' => 29],
            ['display_name' => 'Automatic Routing Configuration', 'route_name' => 'automatic.routing', 'uri' => '/automatic-routing', 'display_order' => 30],
            ['display_name' => 'Notification Configuration', 'route_name' => 'notification.configuration', 'uri' => '/notification-configuration', 'display_order' => 31],
            ['display_name' => 'Priority Configuration', 'route_name' => 'priority.configuration', 'uri' => '/priority-configuration', 'display_order' => 32],
            ['display_name' => 'Severity Configuration', 'route_name' => 'severity.configuration', 'uri' => '/severity-configuration', 'display_order' => 33],
            ['display_name' => 'Issue Category Configuration', 'route_name' => 'issue.category.configuration', 'uri' => '/issue-category-configuration', 'display_order' => 34],
            ['display_name' => 'Vendor Level-2 Mapping', 'route_name' => 'vendor.level2.mapping', 'uri' => '/vendor-level2-mapping', 'display_order' => 35],
            ['display_name' => 'Active / Inactive Status', 'route_name' => 'active.inactive.status', 'uri' => '/active-inactive-status', 'display_order' => 36],
            ['display_name' => 'Change History', 'route_name' => 'change.history', 'uri' => '/change-history', 'display_order' => 37],
            ['display_name' => 'User Activity Log', 'route_name' => 'user.activity.log', 'uri' => '/user-activity-log', 'display_order' => 38],
            ['display_name' => 'System Audit Logs', 'route_name' => 'system.audit.logs', 'uri' => '/system-audit-logs', 'display_order' => 39],
            ['display_name' => 'Vendor State Mapping', 'route_name' => 'vendor.state.mapping', 'uri' => '/vendor-state-mapping', 'display_order' => 40],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(['route_name' => $menu['route_name']], $menu);
        }
    }
}
