<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class InternalIssueController extends Controller
{
    public function create(): View
    {
        return app(PageController::class)->internalIssue();
    }

    public function store(Request $request)
    {
        return app(PageController::class)->storeInternalIssue($request);
    }
}
