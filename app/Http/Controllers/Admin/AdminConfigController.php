<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminConfigController extends Controller
{
    public function workingHours()
    {
        return view('admin.operational.working-hours');
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
        return view('admin.operational.notification-configuration');
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
