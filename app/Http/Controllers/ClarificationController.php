<?php

namespace App\Http\Controllers;

use App\Models\Clarification;
use App\Models\Requirement;
use Illuminate\Http\Request;
use App\Services\RequirementService;

class ClarificationController extends Controller
{
    public function index()
    {
        $clarifications = Clarification::query()
            ->with([
                'requirement:id,title',
                'user:id,name',
            ])
            ->latest()
            ->paginate(20);

        return view(
            'clarifications.index',
            compact('clarifications')
        );
    }


    public function store(
        Request $request,
        Requirement $requirement
    ) {

        $validated = $request->validate([

            'message' => [
                'required',
                'string',
                'max:5000',
            ],

        ]);


        Clarification::create([

            'requirement_id' =>
                $requirement->id,

            'user_id' =>
                auth()->id(),

            'message' =>
                $validated['message'],

            'status' =>
                'Open',

        ]);


        /*
         * Move requirement into clarification state.
         */
        if (
            $requirement->status !==
            'Clarification Pending'
        ) {

            app(RequirementService::class)
                ->changeStatus(
                    $requirement,
                    'Clarification Pending',
                    'New clarification raised.'
                );
        }


        return back()
            ->with(
                'success',
                'Clarification raised successfully.'
            );
    }


    public function reply(
        Request $request,
        Clarification $clarification
    ) {

        $validated = $request->validate([

            'message' => [
                'required',
                'string',
                'max:5000',
            ],

        ]);


        $clarification->replies()->create([

            'user_id' =>
                auth()->id(),

            'message' =>
                $validated['message'],

        ]);


        return back()
            ->with(
                'success',
                'Reply added successfully.'
            );
    }


    public function close(
        Clarification $clarification
    ) {

        $clarification->update([
            'status' => 'Closed',
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);


        return back()
            ->with(
                'success',
                'Clarification closed successfully.'
            );
    }
}