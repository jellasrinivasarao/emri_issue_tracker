<?php

namespace App\Services;

use App\Models\IssueSla;
use Illuminate\Support\Facades\DB;

class SlaMonitorService
{
    public function monitor(): void
    {
        IssueSla::query()

            ->where('overall_status', '!=', 'COMPLETED')

            ->where('overall_status', '!=', 'CANCELLED')

            ->where('is_paused', 0)

            ->chunkById(
                100,
                function ($slas) {

                    foreach ($slas as $sla) {

                        $this->process($sla);

                    }
                },
                'issue_sla_id'
            );
    }

    protected function process(
        IssueSla $sla
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        if (
            !$sla->response_completed_at
        ) {

            $this->checkResponse($sla);

        }

        /*
        |--------------------------------------------------------------------------
        | Resolution
        |--------------------------------------------------------------------------
        */

        if (
            !$sla->resolution_completed_at
        ) {

            $this->checkResolution($sla);

        }

        /*
        |--------------------------------------------------------------------------
        | Overall status
        |--------------------------------------------------------------------------
        */

        $this->updateOverallStatus($sla);
    }


    protected function checkResponse(
    IssueSla $sla
): void {

    if (
        $sla->response_status === 'BREACHED'
    ) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Warning
    |--------------------------------------------------------------------------
    */

    if (
        $sla->response_warning_at
        &&
        now()->gte(
            $sla->response_warning_at
        )
        &&
        $sla->response_status === 'RUNNING'
    ) {

        $sla->update([

            'response_status' =>
                'WARNING',

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Breach
    |--------------------------------------------------------------------------
    */

    if (
        now()->gte(
            $sla->response_due_at
        )
    ) {

        $sla->update([

            'response_status' =>
                'BREACHED',

            'is_response_breached' =>
                1,

            'response_breached_at' =>
                now(),

        ]);
    }
}


protected function checkResolution(
    IssueSla $sla
): void {

    if (
        $sla->resolution_status === 'BREACHED'
    ) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Warning
    |--------------------------------------------------------------------------
    */

    if (
        $sla->resolution_warning_at
        &&
        now()->gte(
            $sla->resolution_warning_at
        )
        &&
        $sla->resolution_status === 'RUNNING'
    ) {

        $sla->update([

            'resolution_status' =>
                'WARNING',

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Breach
    |--------------------------------------------------------------------------
    */

    if (
        now()->gte(
            $sla->resolution_due_at
        )
    ) {

        $sla->update([

            'resolution_status' =>
                'BREACHED',

            'is_resolution_breached' =>
                1,

            'resolution_breached_at' =>
                now(),

        ]);
    }
}

protected function updateOverallStatus(
    IssueSla $sla
): void {

    if (
        $sla->response_completed_at
        &&
        $sla->resolution_completed_at
    ) {

        $sla->update([
            'overall_status' => 'COMPLETED'
        ]);

        return;
    }

    if (
        $sla->response_status === 'BREACHED'
        ||
        $sla->resolution_status === 'BREACHED'
    ) {

        $sla->update([
            'overall_status' => 'BREACHED'
        ]);

        return;
    }

    if ($sla->is_paused) {

        $sla->update([
            'overall_status' => 'PAUSED'
        ]);

        return;
    }

    $sla->update([
        'overall_status' => 'RUNNING'
    ]);
}

}