<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ItSupportDashboardController extends Controller
{
    public function index(Request $request): View
    {
        return app(PageController::class)->itSupportDashboard($request);
    }

    public function update(Request $request)
    {
        return app(PageController::class)->updateItSupportTicket($request);
    }

    public function previewAttachment(int $id)
    {
        return app(PageController::class)->previewItSupportAttachment($id);
    }

    public function viewAttachment(int $id)
    {
        return app(PageController::class)->viewItSupportAttachment($id);
    }

    public function downloadAttachment(int $id)
    {
        return app(PageController::class)->downloadItSupportAttachment($id);
    }
}
