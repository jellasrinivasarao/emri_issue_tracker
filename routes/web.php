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
use App\Http\Controllers\Admin\MenuMasterController;
use App\Http\Controllers\Admin\PrivilegeMasterController;
use App\Http\Controllers\Admin\RoleMenuMappingController;
use App\Http\Controllers\Admin\RolePrivilegeMappingController;
use App\Http\Controllers\Admin\ProjectApplicationModuleMappingController;
use App\Http\Controllers\Admin\ProjectStateMappingController;
use App\Http\Controllers\Admin\UserRoleMappingController;
use App\Http\Controllers\Admin\UserProjectMappingController;
use App\Http\Controllers\Admin\UserSupportGroupMappingController;
use App\Http\Controllers\Admin\VendorStateMappingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForcePasswordController;
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
    Route::get('/role-dashboard', [PageController::class, 'roleDashboard'])->name('role.dashboard');
    Route::get('/role-issue-dashboard', [PageController::class, 'roleIssueDashboard'])
        ->middleware('menu.access:role.issue.dashboard')
        ->name('role.issue.dashboard');

    Route::post('/role-issue-dashboard/update', [PageController::class, 'updateIssueStatus'])
        ->middleware(['auth', 'menu.access:role.issue.dashboard'])
        ->name('role.issue.update');

    Route::get('/issues', [PageController::class, 'issues'])
        ->middleware('menu.access:issues')
        ->name('issues');

    Route::get('/raise-issue', [PageController::class, 'raiseIssue'])
        ->middleware('menu.access:raise.issue')
        ->name('raise.issue');

    Route::get('/reports', [PageController::class, 'reports'])
        ->middleware('menu.access:reports')
        ->name('reports');

    Route::get('/administration', [PageController::class, 'administration'])
        ->middleware('menu.access:administration')
        ->name('administration');

    Route::redirect('/central-admin', '/role-dashboard')
        ->middleware(['auth', 'menu.access:central.admin'])
        ->name('central.admin');

    Route::redirect('/state-admin', '/role-dashboard')
        ->middleware(['auth', 'menu.access:state.admin'])
        ->name('state.admin');

    Route::redirect('/ho-admin', '/role-dashboard')
        ->middleware(['auth', 'menu.access:ho.admin'])
        ->name('ho.admin');

    Route::redirect('/vendor-admin', '/role-dashboard')
        ->middleware(['auth', 'menu.access:vendor.admin'])
        ->name('vendor.admin');

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

    Route::get('/privilege-master', [PrivilegeMasterController::class, 'index'])
        ->middleware(['auth','menu.access:privilege.master'])
        ->name('privilege.master');
    Route::post('/privilege-master', [PrivilegeMasterController::class, 'store'])
        ->middleware(['auth','menu.access:privilege.master'])
        ->name('privilege.master.store');

    Route::put('/privilege-master/{privilege_id}', [PrivilegeMasterController::class, 'update'])
        ->middleware(['auth','menu.access:privilege.master'])
        ->name('privilege.master.update');

    Route::post('/privilege-master/{privilege_id}/toggle', [PrivilegeMasterController::class, 'toggle'])
        ->middleware(['auth','menu.access:privilege.master'])
        ->name('privilege.master.toggle');

    Route::get('/user-role-mapping', [UserRoleMappingController::class, 'index'])
        ->middleware(['auth','menu.access:user.role.mapping'])
        ->name('user.role.mapping');

    Route::get('/user-project-mapping', [UserProjectMappingController::class, 'index'])
        ->middleware(['auth','menu.access:user.project.mapping'])
        ->name('user.project.mapping');

    Route::get('/user-support-group-mapping', [UserSupportGroupMappingController::class, 'index'])
        ->middleware(['auth','menu.access:user.support.group.mapping'])
        ->name('user.support.group.mapping');

    // Forced password change for first-time logins
    Route::get('/password/force-change', [ForcePasswordController::class, 'show'])->name('password.force.change');
    Route::post('/password/force-change', [ForcePasswordController::class, 'update'])->name('password.force.change.update');

    Route::get('/project-application-module', [ProjectApplicationModuleMappingController::class, 'index'])
        ->middleware(['auth','menu.access:project.application.module.mapping'])
        ->name('project.application.module.mapping');

    Route::post('/project-application-module', [ProjectApplicationModuleMappingController::class, 'store'])
        ->middleware(['auth','menu.access:project.application.module.mapping'])
        ->name('project.application.module.mapping.store');

    Route::get('/project-application-module/options', [ProjectApplicationModuleMappingController::class, 'options'])
        ->middleware(['auth','menu.access:project.application.module.mapping'])
        ->name('project.application.module.mapping.options');

    Route::put('/project-application-module/{mapping_id}', [ProjectApplicationModuleMappingController::class, 'update'])
        ->middleware(['auth','menu.access:project.application.module.mapping'])
        ->name('project.application.module.mapping.update');

    Route::post('/project-application-module/{mapping_id}/toggle', [ProjectApplicationModuleMappingController::class, 'toggle'])
        ->middleware(['auth','menu.access:project.application.module.mapping'])
        ->name('project.application.module.mapping.toggle');
    Route::get('/project-state-mapping', [ProjectStateMappingController::class, 'index'])
        ->middleware(['auth','menu.access:project.state.mapping'])
        ->name('project.state.mapping');

    Route::post('/project-state-mapping', [ProjectStateMappingController::class, 'store'])
        ->middleware(['auth','menu.access:project.state.mapping'])
        ->name('project.state.mapping.store');

    Route::get('/project-state-mapping/options', [ProjectStateMappingController::class, 'options'])
        ->middleware(['auth','menu.access:project.state.mapping'])
        ->name('project.state.mapping.options');

    Route::put('/project-state-mapping/{mapping_id}', [ProjectStateMappingController::class, 'update'])
        ->middleware(['auth','menu.access:project.state.mapping'])
        ->name('project.state.mapping.update');

    Route::post('/project-state-mapping/{mapping_id}/toggle', [ProjectStateMappingController::class, 'toggle'])
        ->middleware(['auth','menu.access:project.state.mapping'])
        ->name('project.state.mapping.toggle');
    Route::get('/vendor-state-mapping', [VendorStateMappingController::class, 'index'])
        ->middleware(['auth','menu.access:vendor.state.mapping'])
        ->name('vendor.state.mapping');

    Route::post('/vendor-state-mapping', [VendorStateMappingController::class, 'store'])
        ->middleware(['auth','menu.access:vendor.state.mapping'])
        ->name('vendor.state.mapping.store');

    Route::get('/vendor-state-mapping/options', [VendorStateMappingController::class, 'options'])
        ->middleware(['auth','menu.access:vendor.state.mapping'])
        ->name('vendor.state.mapping.options');

    Route::put('/vendor-state-mapping/{mapping_id}', [VendorStateMappingController::class, 'update'])
        ->middleware(['auth','menu.access:vendor.state.mapping'])
        ->name('vendor.state.mapping.update');

    Route::post('/vendor-state-mapping/{mapping_id}/toggle', [VendorStateMappingController::class, 'toggle'])
        ->middleware(['auth','menu.access:vendor.state.mapping'])
        ->name('vendor.state.mapping.toggle');
    Route::get('/menu-master', [MenuMasterController::class, 'index'])
        ->middleware(['auth','menu.access:menu.master'])
        ->name('menu.master');

    Route::get('/role-menu-mapping', [RoleMenuMappingController::class, 'index'])
        ->middleware(['auth','menu.access:role.menu.mapping'])
        ->name('role.menu.mapping');

    Route::get('/role-privilege-mapping', [RolePrivilegeMappingController::class, 'index'])
        ->middleware(['auth','menu.access:role.privilege.mapping'])
        ->name('role.privilege.mapping');

    Route::post('/role-privilege-mapping', [RolePrivilegeMappingController::class, 'store'])
        ->middleware(['auth','menu.access:role.privilege.mapping'])
        ->name('role.privilege.mapping.store');

    Route::put('/role-privilege-mapping/{role_privilege_id}', [RolePrivilegeMappingController::class, 'update'])
        ->middleware(['auth','menu.access:role.privilege.mapping'])
        ->name('role.privilege.mapping.update');

    Route::post('/role-privilege-mapping/{role_privilege_id}/toggle', [RolePrivilegeMappingController::class, 'toggle'])
        ->middleware(['auth','menu.access:role.privilege.mapping'])
        ->name('role.privilege.mapping.toggle');

    Route::get('/working-hours', [PageController::class, 'genericAdminPage'])->middleware('menu.access:working.hours')->name('working.hours');

    Route::get('/holiday-calendar', [PageController::class, 'genericAdminPage'])->middleware('menu.access:holiday.calendar')->name('holiday.calendar');

    Route::get('/sla-configuration', [PageController::class, 'genericAdminPage'])->middleware('menu.access:sla.configuration')->name('sla.configuration');

    Route::get('/automatic-routing', [PageController::class, 'genericAdminPage'])->middleware('menu.access:automatic.routing')->name('automatic.routing');

    Route::get('/notification-configuration', [PageController::class, 'genericAdminPage'])->middleware('menu.access:notification.configuration')->name('notification.configuration');

    Route::get('/mail-configuration', [PageController::class, 'mailConfiguration'])->middleware('menu.access:mail.configuration')->name('mail.configuration');

    Route::get('/priority-configuration', [PageController::class, 'genericAdminPage'])->middleware('menu.access:priority.configuration')->name('priority.configuration');

    Route::get('/severity-configuration', [PageController::class, 'genericAdminPage'])->middleware('menu.access:severity.configuration')->name('severity.configuration');

    Route::get('/issue-category-configuration', [PageController::class, 'genericAdminPage'])->middleware('menu.access:issue.category.configuration')->name('issue.category.configuration');

    Route::get('/vendor-level2-mapping', [PageController::class, 'genericAdminPage'])->middleware('menu.access:vendor.level2.mapping')->name('vendor.level2.mapping');

    Route::get('/active-inactive-status', [PageController::class, 'genericAdminPage'])->middleware('menu.access:active.inactive.status')->name('active.inactive.status');

    Route::get('/change-history', [PageController::class, 'genericAdminPage'])->middleware('menu.access:change.history')->name('change.history');

    Route::get('/user-activity-log', [PageController::class, 'genericAdminPage'])->middleware('menu.access:user.activity.log')->name('user.activity.log');

    Route::get('/system-audit-logs', [PageController::class, 'genericAdminPage'])->middleware('menu.access:system.audit.logs')->name('system.audit.logs');

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

require __DIR__.'/auth.php';

