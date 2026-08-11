<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MailConfiguration;
use Illuminate\View\View;

class AdminConfigController extends Controller
{
    public function workingHours()
    {
        // Redirect to the canonical working calendars index (paginated)
        return redirect()->route('working.calendars');
    }

    public function holidayCalendar()
    {
        return view('admin.operational.holiday-calendar');
    }

    public function slaConfiguration()
    {
        return view('admin.operational.sla-configuration');
    }

    public function automaticRouting()
    {
        return view('admin.operational.automatic-routing');
    }

    public function notificationConfiguration()
    {
        $user = auth()->user();

        $statesQuery = DB::table('mst_state')
            ->select('state_id', 'state_name')
            ->where('is_active', 1)
            ->orderBy('state_name');

        $configurationQuery = MailConfiguration::query()->orderByDesc('mail_configuration_id');

        if ($user?->hasRole('Vendor Admin') && ! empty($user->vendor_id)) {
            $states = DB::table('map_vendor_state as m')
                ->join('mst_state as s', 'm.state_id', '=', 's.state_id')
                ->where('m.vendor_id', $user->vendor_id)
                ->where('s.is_active', 1)
                ->distinct()
                ->orderBy('s.state_name')
                ->get(['s.state_id', 's.state_name']);

            $stateIds = $states->pluck('state_id')->all();
            if (! empty($stateIds)) {
                $configurationQuery->whereIn('state_id', $stateIds);
            }
        } elseif ($user?->hasRole('State Admin') && ! empty($user->state_id)) {
            $stateIds = explode(',', (string) $user->state_id);
            $stateIds = array_filter(array_map('trim', $stateIds), fn ($id) => $id !== '');
            $states = $statesQuery->whereIn('state_id', $stateIds)->get();
            $configurationQuery->whereIn('state_id', $stateIds);
        } else {
            $states = $statesQuery->get();
        }

        $routeName = 'notification.configuration';
        $permissions = [
            'view' => $user?->hasPrivilegeOnRoute($routeName, 'view'),
            'create' => $user?->hasPrivilegeOnRoute($routeName, 'create'),
            'edit' => $user?->hasPrivilegeOnRoute($routeName, 'edit'),
            'delete' => $user?->hasPrivilegeOnRoute($routeName, 'delete'),
            'export' => $user?->hasPrivilegeOnRoute($routeName, 'export'),
            'activate' => $user?->hasPrivilegeOnRoute($routeName, 'activate'),
            'deactivate' => $user?->hasPrivilegeOnRoute($routeName, 'deactivate'),
        ];

        $mailConfigurations = $configurationQuery->paginate(20);

        return view('admin.operational.notification-configuration', [
            'states' => $states,
            'permissions' => $permissions,
            'mailConfigurations' => $mailConfigurations,
            'title' => 'Notification Configuration',
        ]);
    }

    public function priorityConfiguration()
    {
        return view('admin.operational.priority-configuration');
    }

    public function severityConfiguration()
    {
        return view('admin.operational.severity-configuration');
    }

    public function issueCategoryConfiguration()
    {
        return view('admin.operational.issue-category-configuration');
    }

    public function vendorLevel2Mapping()
    {
        return view('admin.operational.vendor-level2-mapping');
    }
}
