<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SlaConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | Find Working Calendars
        |--------------------------------------------------------------------------
        |
        | The SLA should use an existing working calendar.
        | If INDIA_BUSINESS does not exist, the value remains NULL.
        |
        */

        $indiaCalendarId = DB::table('mst_working_calendar')
            ->where('calendar_code', 'IND_EMRI')
            ->value('calendar_id');

        /*
        |--------------------------------------------------------------------------
        | SLA Master Data
        |--------------------------------------------------------------------------
        */

        $slas = [

            [
                'sla_code' => 'SLA-P1-CRITICAL',
                'sla_name' => 'Critical - Priority 1',

                'description' =>
                    'Critical production issue requiring immediate response and resolution.',

                'response_sla_hours' => 1.00,
                'resolution_sla_hours' => 4.00,
                'escalation_sla_hours' => 2.00,

                'support_level' => 1,

                'calendar_id' => $indiaCalendarId,

                'is_active' => true,
            ],

            [
                'sla_code' => 'SLA-P2-HIGH',
                'sla_name' => 'High - Priority 2',

                'description' =>
                    'High priority production issue requiring expedited resolution.',

                'response_sla_hours' => 2.00,
                'resolution_sla_hours' => 8.00,
                'escalation_sla_hours' => 4.00,

                'support_level' => 1,

                'calendar_id' => $indiaCalendarId,

                'is_active' => true,
            ],

            [
                'sla_code' => 'SLA-P3-MEDIUM',
                'sla_name' => 'Medium - Priority 3',

                'description' =>
                    'Medium priority issue handled within standard business SLA.',

                'response_sla_hours' => 4.00,
                'resolution_sla_hours' => 24.00,
                'escalation_sla_hours' => 12.00,

                'support_level' => 2,

                'calendar_id' => $indiaCalendarId,

                'is_active' => true,
            ],

            [
                'sla_code' => 'SLA-P4-LOW',
                'sla_name' => 'Low - Priority 4',

                'description' =>
                    'Low priority issue handled within normal operational SLA.',

                'response_sla_hours' => 8.00,
                'resolution_sla_hours' => 48.00,
                'escalation_sla_hours' => 24.00,

                'support_level' => 2,

                'calendar_id' => $indiaCalendarId,

                'is_active' => true,
            ],

            [
                'sla_code' => 'SLA-VENDOR-L2',
                'sla_name' => 'Vendor Level-2 Standard',

                'description' =>
                    'Standard SLA for Vendor Level-2 intervention.',

                'response_sla_hours' => 4.00,
                'resolution_sla_hours' => 24.00,
                'escalation_sla_hours' => 12.00,

                'support_level' => 2,

                'calendar_id' => $indiaCalendarId,

                'is_active' => true,
            ],

            [
                'sla_code' => 'SLA-HO-L1',
                'sla_name' => 'HO IT Level-1 Standard',

                'description' =>
                    'Standard SLA for HO IT Level-1 intervention.',

                'response_sla_hours' => 2.00,
                'resolution_sla_hours' => 8.00,
                'escalation_sla_hours' => 4.00,

                'support_level' => 1,

                'calendar_id' => $indiaCalendarId,

                'is_active' => true,
            ],
        ];

        foreach ($slas as $sla) {

            DB::table('mst_sla_configuration')->updateOrInsert(
                [
                    'sla_code' => $sla['sla_code'],
                ],
                [
                    'sla_name' => $sla['sla_name'],
                    'description' => $sla['description'],

                    'response_sla_hours' =>
                        $sla['response_sla_hours'],

                    'resolution_sla_hours' =>
                        $sla['resolution_sla_hours'],

                    'escalation_sla_hours' =>
                        $sla['escalation_sla_hours'],

                    'support_level' =>
                        $sla['support_level'],

                    'calendar_id' =>
                        $sla['calendar_id'],

                    'is_active' =>
                        $sla['is_active'],

                    'updated_at' => $now,
                ]
            );
        }
    }
}