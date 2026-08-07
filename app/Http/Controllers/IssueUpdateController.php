<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\Issue;
use App\Models\IssueSla;
use App\Models\IssueUpdate;
use App\Services\IssueSlaService;
use Illuminate\Support\Facades\DB;

class IssueUpdateController extends Controller
{
    protected IssueSlaService $issueSlaService;

    public function __construct(
        IssueSlaService $issueSlaService
    ) {
        $this->issueSlaService = $issueSlaService;
    }

    public function store(
        Request $request,
        Issue $issue
    ) {

        $request->validate([
            'comment' => [
                'required',
                'string',
                'max:5000'
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $issue
        ) {

            /*
            |--------------------------------------------------------------------------
            | Create Issue Update
            |--------------------------------------------------------------------------
            */

            $update = IssueUpdate::create([

                'issue_id' => $issue->issue_id,

                'updated_by' => auth()->id(),

                'comment' => $request->comment,

                'old_status_id' =>
                    $issue->status_id,

                'new_status_id' =>
                    $issue->status_id,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Complete Response SLA
            |--------------------------------------------------------------------------
            */

            $sla = IssueSla::where(
                'issue_id',
                $issue->issue_id
            )->first();

            if ($sla) {

                $this->issueSlaService->markResponseCompleted($sla);

            }

        });

        return back()->with(
            'success',
            'Issue update added successfully.'
        );
    }
}