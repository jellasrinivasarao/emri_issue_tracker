<?php

use App\Http\Controllers\Admin\ApplicationMasterController;
use App\Http\Controllers\Admin\ModuleMasterController;
use App\Http\Controllers\Admin\ProjectMasterController;
use App\Http\Controllers\Admin\ServiceMasterController;
use App\Http\Controllers\Admin\RoleMasterController;
use App\Http\Controllers\Admin\StateMasterController;
use App\Http\Controllers\Admin\SupportGroupMasterController;
use App\Http\Controllers\Admin\UserMasterController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/role-dashboard', 'role-dashboard')->name('role.dashboard');

    Route::view('/issues', 'pages.issues')
        ->middleware('menu.access:issues')
        ->name('issues');

    Route::view('/raise-issue', 'pages.raise-issue')
        ->middleware('menu.access:raise.issue')
        ->name('raise.issue');

    Route::view('/reports', 'pages.reports')
        ->middleware('menu.access:reports')
        ->name('reports');

    Route::view('/administration', 'pages.administration')
        ->middleware('menu.access:administration')
        ->name('administration');

    Route::view('/central-admin', 'pages.generic-admin-page', [
        'title' => 'Main Dashboard',
        'description' => 'View the main dashboard, system summaries, and overall administration status.',
    ])->middleware('menu.access:central.admin')->name('central.admin');

    Route::view('/state-admin', 'pages.generic-admin-page', [
        'title' => 'State Admin',
        'description' => 'Access state administration capabilities and manage state-specific settings.',
    ])->middleware('menu.access:state.admin')->name('state.admin');

    Route::view('/ho-admin', 'pages.generic-admin-page', [
        'title' => 'HO Admin',
        'description' => 'Manage head office administration and central operational controls.',
    ])->middleware('menu.access:ho.admin')->name('ho.admin');

    Route::view('/vendor-admin', 'pages.generic-admin-page', [
        'title' => 'Vendor Admin',
        'description' => 'Manage vendor administration tasks and vendor-specific operations.',
    ])->middleware('menu.access:vendor.admin')->name('vendor.admin');

    Route::get('/state-master', [StateMasterController::class, 'index'])
        ->middleware(['auth', 'menu.access:state.master'])
        ->name('state.master');

    Route::post('/state-master', [StateMasterController::class, 'store'])
        ->middleware(['auth', 'menu.access:state.master'])
        ->name('state.master.store');

    Route::put('/state-master/{state_id}', [StateMasterController::class, 'update'])
        ->middleware(['auth', 'menu.access:state.master'])
        ->name('state.master.update');

    Route::post('/state-master/{state_id}/toggle', [StateMasterController::class, 'toggle'])
        ->middleware(['auth', 'menu.access:state.master'])
        ->name('state.master.toggle');

    Route::get('/vendor-master', [VendorController::class, 'index'])
        ->middleware(['auth', 'menu.access:vendor.master'])
        ->name('vendor.master');

    Route::post('/vendor-master', [VendorController::class, 'store'])
        ->middleware(['auth', 'menu.access:vendor.master'])
        ->name('vendor.master.store');

    Route::put('/vendor-master/{vendor}', [VendorController::class, 'update'])
        ->middleware(['auth', 'menu.access:vendor.master'])
        ->name('vendor.master.update');

    Route::post('/vendor-master/{vendor}/toggle', [VendorController::class, 'toggle'])
        ->middleware(['auth', 'menu.access:vendor.master'])
        ->name('vendor.master.toggle');

    Route::get('/service-master', [ServiceMasterController::class, 'index'])
        ->middleware('menu.access:service.master')
        ->name('service.master');

    Route::post('/service-master', [ServiceMasterController::class, 'store'])
        ->middleware(['auth','menu.access:service.master'])
        ->name('service.master.store');

    Route::put('/service-master/{service_id}', [ServiceMasterController::class, 'update'])
        ->middleware(['auth','menu.access:service.master'])
        ->name('service.master.update');

    Route::post('/service-master/{service_id}/toggle', [ServiceMasterController::class, 'toggle'])
        ->middleware(['auth','menu.access:service.master'])
        ->name('service.master.toggle');

    Route::get('/project-master', [ProjectMasterController::class, 'index'])
        ->middleware('menu.access:project.master')
        ->name('project.master');

    Route::post('/project-master', [ProjectMasterController::class, 'store'])
        ->middleware(['auth','menu.access:project.master'])
        ->name('project.master.store');

    Route::put('/project-master/{project_id}', [ProjectMasterController::class, 'update'])
        ->middleware(['auth','menu.access:project.master'])
        ->name('project.master.update');

    Route::post('/project-master/{project_id}/toggle', [ProjectMasterController::class, 'toggle'])
        ->middleware(['auth','menu.access:project.master'])
        ->name('project.master.toggle');


    Route::get('/application-master', [ApplicationMasterController::class, 'index'])
        ->middleware('menu.access:application.master')
        ->name('application.master');

    Route::post('/application-master', [ApplicationMasterController::class, 'store'])
        ->middleware(['auth','menu.access:application.master'])
        ->name('application.master.store');

    Route::put('/application-master/{application_id}', [ApplicationMasterController::class, 'update'])
        ->middleware(['auth','menu.access:application.master'])
        ->name('application.master.update');

    Route::post('/application-master/{application_id}/toggle', [ApplicationMasterController::class, 'toggle'])
        ->middleware(['auth','menu.access:application.master'])
        ->name('application.master.toggle');

    Route::get('/module-master', [ModuleMasterController::class, 'index'])
        ->middleware('menu.access:module.master')
        ->name('module.master');

    Route::post('/module-master', [ModuleMasterController::class, 'store'])
        ->middleware(['auth','menu.access:module.master'])
        ->name('module.master.store');

    Route::put('/module-master/{module_id}', [ModuleMasterController::class, 'update'])
        ->middleware(['auth','menu.access:module.master'])
        ->name('module.master.update');

    Route::post('/module-master/{module_id}/toggle', [ModuleMasterController::class, 'toggle'])
        ->middleware(['auth','menu.access:module.master'])
        ->name('module.master.toggle');

    Route::get('/support-group-master', [SupportGroupMasterController::class, 'index'])
        ->middleware('menu.access:support-group.master')
        ->name('support-group.master');

    Route::post('/support-group-master', [SupportGroupMasterController::class, 'store'])
        ->middleware(['auth','menu.access:support-group.master'])
        ->name('support-group.master.store');

    Route::put('/support-group-master/{support_group_id}', [SupportGroupMasterController::class, 'update'])
        ->middleware(['auth','menu.access:support-group.master'])
        ->name('support-group.master.update');

    Route::post('/support-group-master/{support_group_id}/toggle', [SupportGroupMasterController::class, 'toggle'])
        ->middleware(['auth','menu.access:support-group.master'])
        ->name('support-group.master.toggle');

    Route::get('/user-master', [UserMasterController::class, 'index'])
        ->middleware(['auth','menu.access:user.master'])
        ->name('user.master');

    Route::post('/user-master', [UserMasterController::class, 'store'])
        ->middleware(['auth','menu.access:user.master'])
        ->name('user.master.store');

    Route::put('/user-master/{user_id}', [UserMasterController::class, 'update'])
        ->middleware(['auth','menu.access:user.master'])
        ->name('user.master.update');

    Route::post('/user-master/{user_id}/toggle', [UserMasterController::class, 'toggle'])
        ->middleware(['auth','menu.access:user.master'])
        ->name('user.master.toggle');

    Route::get('/role-master', [RoleMasterController::class, 'index'])
        ->middleware(['auth','menu.access:role.master'])
        ->name('role.master');

    Route::post('/role-master', [RoleMasterController::class, 'store'])
        ->middleware(['auth','menu.access:role.master'])
        ->name('role.master.store');

    Route::put('/role-master/{role_id}', [RoleMasterController::class, 'update'])
        ->middleware(['auth','menu.access:role.master'])
        ->name('role.master.update');

    Route::post('/role-master/{role_id}/toggle', [RoleMasterController::class, 'toggle'])
        ->middleware(['auth','menu.access:role.master'])
        ->name('role.master.toggle');

    Route::view('/privilege-master', 'pages.generic-admin-page', [
        'title' => 'Privilege Master',
        'description' => 'Manage privileges, privilege codes, and access permissions.',
    ])->middleware('menu.access:privilege.master')->name('privilege.master');

    Route::view('/user-role-mapping', 'pages.generic-admin-page', [
        'title' => 'Userâ€“Role Mapping',
        'description' => 'Map users to roles and manage user role assignments.',
    ])->middleware('menu.access:user.role.mapping')->name('user.role.mapping');

    Route::view('/user-project-mapping', 'pages.generic-admin-page', [
        'title' => 'Userâ€“Project Mapping',
        'description' => 'Manage user access to projects and project assignments.',
    ])->middleware('menu.access:user.project.mapping')->name('user.project.mapping');

    Route::view('/user-support-group-mapping', 'pages.generic-admin-page', [
        'title' => 'Userâ€“Support Group Mapping',
        'description' => 'Manage user membership in support groups.',
    ])->middleware('menu.access:user.support.group.mapping')->name('user.support.group.mapping');

    Route::view('/menu-master', 'pages.generic-admin-page', [
        'title' => 'Menu Master',
        'description' => 'Manage menu items, route access, and sidebar navigation entries.',
    ])->middleware('menu.access:menu.master')->name('menu.master');

    Route::view('/role-menu-mapping', 'pages.generic-admin-page', [
        'title' => 'Role–Menu Mapping',
        'description' => 'Manage role permissions and menu access mapping.',
    ])->middleware('menu.access:role.menu.mapping')->name('role.menu.mapping');

    Route::view('/role-privilege-mapping', 'pages.generic-admin-page', [
        'title' => 'Roleâ€“Privilege Mapping',
        'description' => 'Map roles to privileges and control role-based actions.',
    ])->middleware('menu.access:role.privilege.mapping')->name('role.privilege.mapping');

    Route::view('/working-hours', 'pages.generic-admin-page', [
        'title' => 'Working Hours',
        'description' => 'Manage working hours and shift schedules.',
    ])->middleware('menu.access:working.hours')->name('working.hours');

    Route::view('/holiday-calendar', 'pages.generic-admin-page', [
        'title' => 'Holiday Calendar',
        'description' => 'Manage public holidays and calendar events.',
    ])->middleware('menu.access:holiday.calendar')->name('holiday.calendar');

    Route::view('/sla-configuration', 'pages.generic-admin-page', [
        'title' => 'SLA Configuration',
        'description' => 'Configure SLA rules, targets, and escalation conditions.',
    ])->middleware('menu.access:sla.configuration')->name('sla.configuration');

    Route::view('/automatic-routing', 'pages.generic-admin-page', [
        'title' => 'Automatic Routing Configuration',
        'description' => 'Configure automatic issue routing rules.',
    ])->middleware('menu.access:automatic.routing')->name('automatic.routing');

    Route::view('/notification-configuration', 'pages.generic-admin-page', [
        'title' => 'Notification Configuration',
        'description' => 'Manage notification templates and alerts.',
    ])->middleware('menu.access:notification.configuration')->name('notification.configuration');

    Route::view('/priority-configuration', 'pages.generic-admin-page', [
        'title' => 'Priority Configuration',
        'description' => 'Manage priority definitions and priority levels.',
    ])->middleware('menu.access:priority.configuration')->name('priority.configuration');

    Route::view('/severity-configuration', 'pages.generic-admin-page', [
        'title' => 'Severity Configuration',
        'description' => 'Manage severity levels and severity descriptors.',
    ])->middleware('menu.access:severity.configuration')->name('severity.configuration');

    Route::view('/issue-category-configuration', 'pages.generic-admin-page', [
        'title' => 'Issue Category Configuration',
        'description' => 'Manage issue categories and categorization rules.',
    ])->middleware('menu.access:issue.category.configuration')->name('issue.category.configuration');

    Route::view('/vendor-level2-mapping', 'pages.generic-admin-page', [
        'title' => 'Vendor Level-2 Mapping',
        'description' => 'Manage vendor level-2 mappings and escalation groups.',
    ])->middleware('menu.access:vendor.level2.mapping')->name('vendor.level2.mapping');

    Route::view('/active-inactive-status', 'pages.generic-admin-page', [
        'title' => 'Active / Inactive Status',
        'description' => 'View and manage active/inactive status for users and records.',
    ])->middleware('menu.access:active.inactive.status')->name('active.inactive.status');

    Route::view('/change-history', 'pages.generic-admin-page', [
        'title' => 'Change History',
        'description' => 'Review change history and audit trails.',
    ])->middleware('menu.access:change.history')->name('change.history');

    Route::view('/user-activity-log', 'pages.generic-admin-page', [
        'title' => 'User Activity Log',
        'description' => 'Review user activity and session logs.',
    ])->middleware('menu.access:user.activity.log')->name('user.activity.log');

    Route::view('/system-audit-logs', 'pages.generic-admin-page', [
        'title' => 'System Audit Logs',
        'description' => 'Review system audit logs and governance reports.',
    ])->middleware('menu.access:system.audit.logs')->name('system.audit.logs');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Local debug route to return the authenticated user's row (hidden password hash).
if (app()->environment('local')) {
    Route::get('/debug-auth-user', function () {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['user' => null], 401);
        }
        return response()->json($user->makeHidden(['password_hash']));
    })->middleware('auth')->name('debug.auth.user');
}

use App\Http\Controllers\Admin\WorkingCalendarController;
use App\Http\Controllers\Admin\WorkingScheduleController;
use App\Http\Controllers\Admin\CalendarHolidayController;
use App\Http\Controllers\IssueController;
##################################04/08/2026

Route::middleware(['auth'])->group(function () {

    Route::resource('/working-calendars',WorkingCalendarController::class)->parameters(['working-calendars' => 'calendar'])->names('working-calendars');
    Route::post('/{calendar_id}/toggle', [WorkingCalendarController::class, 'toggle'])->name('working-calendars.toggle');

    Route::post('/issues/determine-route',[IssueController::class, 'determineRoute'])->name('issues.determine-route');
    
    Route::resource('/working-schedules', WorkingScheduleController::class)->parameters(['working-schedules' => 'schedule'])->names('working-schedules');
    Route::post('/{schedule_id}/toggle',[WorkingScheduleController::class, 'toggle'])->name('working-schedules.toggle');


    Route::resource('/calendar-holidays', CalendarHolidayController::class)->parameters(['calendar-holidays' => 'holiday'])->names('calendar-holidays');
    
    Route::post('/{holiday_id}/toggle',[CalendarHolidayController::class, 'toggle'])->name('calendar-holidays.toggle');
});



use App\Http\Controllers\ProjectSupportConfigurationController;
use App\Http\Controllers\IssueRoutingRuleController;


Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Project Support Configuration
    |--------------------------------------------------------------------------
    */

    Route::get('/project-support-configurations',[ProjectSupportConfigurationController::class, 'index'])->name('project-support-configurations.index');

    Route::post('/project-support-configurations',[ProjectSupportConfigurationController::class, 'store'])->name('project-support-configurations.store');

    Route::put('/project-support-configurations/{projectSupportConfiguration}',[ProjectSupportConfigurationController::class, 'update'])->name('project-support-configurations.update');

    Route::post('/project-support-configurations/{projectSupportConfiguration}/toggle',[ProjectSupportConfigurationController::class, 'toggle'])->name('project-support-configurations.toggle');


    /*
    |--------------------------------------------------------------------------
    | Issue Routing Rules
    |----------
    */
    
    Route::get('/issue-routing-rules',[IssueRoutingRuleController::class, 'index'])->name('issue-routing-rules.index');
    Route::post('/issue-routing-rules',[IssueRoutingRuleController::class, 'store'])->name('issue-routing-rules.store');

    Route::put('/issue-routing-rules/{issueRoutingRule}',[IssueRoutingRuleController::class, 'update'])->name('issue-routing-rules.update');

    Route::post('/issue-routing-rules/{issueRoutingRule}/toggle',[IssueRoutingRuleController::class, 'toggle'])->name('issue-routing-rules.toggle');
});
require __DIR__.'/auth.php';