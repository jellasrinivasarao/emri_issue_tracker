<?php

namespace App\Services;

use App\Models\Requirement;
use App\Models\RequirementFile;
use App\Models\RequirementStatusHistory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class RequirementService
{
    /*
    |--------------------------------------------------------------------------
    | Status lifecycle
    |--------------------------------------------------------------------------
    */

    public const BRD_RAISED =
        'BRD Raised';

    public const RECEIVED_AT_HO =
        'Received at HO';

    public const SENT_TO_VENDOR =
        'Sent to Vendor';

    public const CLARIFICATION_PENDING =
        'Clarification Pending';

    public const IN_PROGRESS =
        'In Progress';

    public const UAT_REQUESTED =
        'UAT Requested';

    public const UAT_IN_PROGRESS =
        'UAT In Progress';

    public const UAT_COMPLETED =
        'UAT Completed';

    public const MOVED_TO_PRODUCTION =
        'Moved to Production';

    public const CLOSED =
        'Closed';

    public const ON_HOLD =
        'On Hold';


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data,
        UploadedFile $brd
    ): Requirement {

        return DB::transaction(function () use (
            $data,
            $brd
        ) {

            $requirement = Requirement::create([

                'requirement_no' =>
                    $this->generateRequirementNumber(),

                'title' =>
                    $data['title'],

                'description' =>
                    $data['description'],

                'state_id' =>
                    $data['state_id'],

                'project_id' =>
                    $data['project_id'],

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

                'status' =>
                    self::BRD_RAISED,

                'created_by' =>
                    auth()->id(),
            ]);


            /*
             * Upload BRD.
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
             * Audit.
             */
            $this->createHistory(
                $requirement,
                null,
                self::BRD_RAISED,
                'Requirement created.'
            );


            return $requirement;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Generate requirement number
    |--------------------------------------------------------------------------
    */

    protected function generateRequirementNumber(): string
    {
        $year = now()->format('Y');

        $last = Requirement::query()
            ->whereYear('created_at', $year)
            ->lockForUpdate()
            ->latest('id')
            ->first();

        $number = $last
            ? ((int) substr(
                $last->requirement_no,
                -4
            )) + 1
            : 1;

        return sprintf(
            'REQ-%s-%04d',
            $year,
            $number
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Requirement $requirement,
        array $data
    ): Requirement {

        $data['updated_by'] =
            auth()->id();

        $requirement->update($data);

        return $requirement->refresh();
    }


    /*
    |--------------------------------------------------------------------------
    | Change status
    |--------------------------------------------------------------------------
    */

    public function changeStatus(
        Requirement $requirement,
        string $newStatus,
        ?string $remarks = null
    ): Requirement {

        return DB::transaction(
            function () use (
                $requirement,
                $newStatus,
                $remarks
            ) {

                $oldStatus =
                    $requirement->status;

                if ($oldStatus === $newStatus) {
                    return $requirement;
                }


                $this->validateTransition(
                    $oldStatus,
                    $newStatus
                );


                $requirement->update([

                    'status' =>
                        $newStatus,

                    'updated_by' =>
                        auth()->id(),
                ]);


                $this->createHistory(

                    $requirement,

                    $oldStatus,

                    $newStatus,

                    $remarks
                );


                return $requirement->refresh();
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Status transition validation
    |--------------------------------------------------------------------------
    */

    protected function validateTransition(
        ?string $from,
        string $to
    ): void {

        $workflow = [

            self::BRD_RAISED => [
                self::RECEIVED_AT_HO,
                self::ON_HOLD,
            ],

            self::RECEIVED_AT_HO => [
                self::SENT_TO_VENDOR,
                self::ON_HOLD,
            ],

            self::SENT_TO_VENDOR => [
                self::CLARIFICATION_PENDING,
                self::IN_PROGRESS,
                self::ON_HOLD,
            ],

            self::CLARIFICATION_PENDING => [
                self::IN_PROGRESS,
                self::ON_HOLD,
            ],

            self::IN_PROGRESS => [
                self::UAT_REQUESTED,
                self::CLARIFICATION_PENDING,
                self::ON_HOLD,
            ],

            self::UAT_REQUESTED => [
                self::UAT_IN_PROGRESS,
                self::ON_HOLD,
            ],

            self::UAT_IN_PROGRESS => [
                self::UAT_COMPLETED,
                self::ON_HOLD,
            ],

            self::UAT_COMPLETED => [
                self::MOVED_TO_PRODUCTION,
                self::ON_HOLD,
            ],

            self::MOVED_TO_PRODUCTION => [
                self::CLOSED,
            ],

            self::ON_HOLD => [
                self::IN_PROGRESS,
                self::CLARIFICATION_PENDING,
            ],

            self::CLOSED => [],
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


    /*
    |--------------------------------------------------------------------------
    | History
    |--------------------------------------------------------------------------
    */

    protected function createHistory(
        Requirement $requirement,
        ?string $from,
        string $to,
        ?string $remarks
    ): void {

        RequirementStatusHistory::create([

            'requirement_id' =>
                $requirement->id,

            'from_status' =>
                $from,

            'to_status' =>
                $to,

            'remarks' =>
                $remarks,

            'changed_by' =>
                auth()->id(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Vendor update
    |--------------------------------------------------------------------------
    */

    public function updateVendorDetails(
        Requirement $requirement,
        array $data
    ): Requirement {

        return DB::transaction(
            function () use (
                $requirement,
                $data
            ) {

                $requirement->update([

                    'man_days' =>
                        $data['man_days'],

                    'timeline' =>
                        $data['timeline'],

                    'delivery_status' =>
                        $data['delivery_status'],

                    'vendor_remarks' =>
                        $data['remarks'] ?? null,

                    'updated_by' =>
                        auth()->id(),
                ]);


                $status =
                    $this->mapVendorStatus(
                        $data['delivery_status']
                    );


                if (
                    $status &&
                    $requirement->status !== $status
                ) {

                    $this->changeStatus(
                        $requirement,
                        $status,
                        $data['remarks'] ?? null
                    );
                }


                return $requirement->refresh();
            }
        );
    }


    protected function mapVendorStatus(
        string $vendorStatus
    ): ?string {

        return match ($vendorStatus) {

            'Requirements Understood' =>
                self::SENT_TO_VENDOR,

            'Development Started' =>
                self::IN_PROGRESS,

            'Development Completed' =>
                self::IN_PROGRESS,

            'Moved to UAT' =>
                self::UAT_REQUESTED,

            'UAT Completed' =>
                self::UAT_COMPLETED,

            'Moved to Production' =>
                self::MOVED_TO_PRODUCTION,

            'On Hold' =>
                self::ON_HOLD,

            default =>
                null,
        };
    }
}