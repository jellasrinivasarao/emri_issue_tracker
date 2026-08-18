<?php

namespace App\Services;

use App\Models\Requirement;
use App\Models\RequirementFile;
use App\Models\RequirementStatusHistory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RequirementService
{
    /**
     * Create requirement and upload BRD.
     */
    public function create(
        array $data,
        UploadedFile $brd
    ): Requirement {

        return DB::transaction(function () use ($data, $brd) {

            $requirement = Requirement::create([

                'title' => $data['title'],

                'description' => $data['description'],

                'state_id' => $data['state_id'],

                'project_id' => $data['project_id'],

                'brd_raised_by' =>
                    $data['brd_raised_by'] ?? null,

                'ho_it_team' =>
                    $data['ho_it_team'],

                'received_at' =>
                    $data['received_at'] ?? null,

                'requested_to_vendor_at' =>
                    $data['requested_to_vendor_at'] ?? null,

                'additional_details' =>
                    $data['additional_details'] ?? null,

                'status' => 'BRD Raised',

                'created_by' =>
                    auth()->id(),

            ]);


            /*
             * Store BRD.
             */
            $path = $brd->store(
                'requirements/' . $requirement->id,
                'private'
            );


            RequirementFile::create([

                'requirement_id' =>
                    $requirement->id,

                'original_name' =>
                    $brd->getClientOriginalName(),

                'file_path' =>
                    $path,

                'mime_type' =>
                    $brd->getMimeType(),

                'file_size' =>
                    $brd->getSize(),

                'uploaded_by' =>
                    auth()->id(),

            ]);


            /*
             * Status history.
             */
            RequirementStatusHistory::create([

                'requirement_id' =>
                    $requirement->id,

                'from_status' =>
                    null,

                'to_status' =>
                    'BRD Raised',

                'remarks' =>
                    'Requirement created.',

                'changed_by' =>
                    auth()->id(),

            ]);


            return $requirement;
        });
    }


    /**
     * Update requirement.
     */
    public function update(
        Requirement $requirement,
        array $data
    ): Requirement {

        return DB::transaction(function () use (
            $requirement,
            $data
        ) {

            $requirement->update($data);

            return $requirement->refresh();
        });
    }


    /**
     * Change requirement status.
     */
    public function changeStatus(
        Requirement $requirement,
        string $newStatus,
        ?string $remarks = null
    ): Requirement {

        return DB::transaction(function () use (
            $requirement,
            $newStatus,
            $remarks
        ) {

            $oldStatus = $requirement->status;

            if ($oldStatus === $newStatus) {
                return $requirement;
            }


            /*
             * Validate status transition.
             */
            $this->validateTransition(
                $oldStatus,
                $newStatus
            );


            $requirement->update([
                'status' => $newStatus,
            ]);


            RequirementStatusHistory::create([

                'requirement_id' =>
                    $requirement->id,

                'from_status' =>
                    $oldStatus,

                'to_status' =>
                    $newStatus,

                'remarks' =>
                    $remarks,

                'changed_by' =>
                    auth()->id(),

            ]);


            return $requirement->refresh();
        });
    }


    /**
     * Validate status lifecycle.
     */
    protected function validateTransition(
        ?string $from,
        string $to
    ): void {

        $workflow = [

            'BRD Raised' => [
                'Received at HO',
                'On Hold',
            ],

            'Received at HO' => [
                'Sent to Vendor',
                'On Hold',
            ],

            'Sent to Vendor' => [
                'Clarification Pending',
                'In Progress',
                'On Hold',
            ],

            'Clarification Pending' => [
                'In Progress',
                'On Hold',
            ],

            'In Progress' => [
                'UAT Requested',
                'Clarification Pending',
                'On Hold',
            ],

            'UAT Requested' => [
                'UAT In Progress',
                'On Hold',
            ],

            'UAT In Progress' => [
                'UAT Completed',
                'On Hold',
            ],

            'UAT Completed' => [
                'Moved to Production',
                'On Hold',
            ],

            'Moved to Production' => [
                'Closed',
            ],

            'On Hold' => [
                'In Progress',
                'Clarification Pending',
            ],

            'Closed' => [],
        ];


        if (
            !isset($workflow[$from]) ||
            !in_array(
                $to,
                $workflow[$from],
                true
            )
        ) {

            throw new \RuntimeException(
                "Invalid status transition: {$from} → {$to}"
            );
        }
    }
}