<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function roleDashboard(): View
    {
        return view('role-dashboard');
    }

    public function issues(): View
    {
        return view('pages.issues');
    }

    public function raiseIssue(): View
    {
        return view('pages.raise-issue');
    }

    public function reports(): View
    {
        return view('pages.reports');
    }

    public function administration(): View
    {
        return view('pages.administration');
    }

    public function genericAdminPage(Request $request): View
    {
        $title = $request->query('title', 'Administration');
        $description = $request->query('description', 'Manage administrative settings.');

        return view('pages.generic-admin-page', compact('title', 'description'));
    }
}
