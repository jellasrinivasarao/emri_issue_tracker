<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportRoutingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed a default support configuration for one project.
        DB::table('mst_project_support_configuration')->updateOrInsert(
            [
                'project_id' => 1,
                'config_code' => 'DEFAULT_SUPPORT_CFG',
            ],
            [
                'config_name' => 'Default Support Configuration',
                'default_support_level' => 1,
                'default_team_type' => 'HO_IT',
                'sla_hours' => 4.00,
                'description' => 'Default active support configuration for project 1.',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Seed a default HO IT support team.
        DB::table('mst_support_team')->updateOrInsert(
            [
                'team_code' => 'HO_IT_LEVEL_1',
            ],
            [
                'team_name' => 'HO IT Level 1',
                'team_type' => 'HO_IT',
                'support_level' => 1,
                'email' => 'hoit@example.com',
                'phone' => '0000000000',
                'description' => 'Default HO IT support team.',
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Seed a default routing rule for project 1.
        $configuration = DB::table('mst_project_support_configuration')
            ->where('project_id', 1)
            ->where('config_code', 'DEFAULT_SUPPORT_CFG')
            ->first();

        $team = DB::table('mst_support_team')
            ->where('team_code', 'HO_IT_LEVEL_1')
            ->first();

        if ($configuration && $team) {
            DB::table('mst_issue_routing_rule')->updateOrInsert(
                [
                    'rule_code' => 'DEFAULT_HO_IT_RULE',
                ],
                [
                    'rule_name' => 'Default HO IT Routing Rule',
                    'project_id' => 1,
                    'support_configuration_id' => $configuration->support_configuration_id,
                    'issue_category_id' => null,
                    'issue_type_id' => null,
                    'priority_id' => null,
                    'support_level' => 1,
                    'support_team_id' => $team->support_team_id,
                    'sla_hours' => 4.00,
                    'routing_priority' => 1,
                    'description' => 'Default routing rule for project 1 and HO IT team.',
                    'is_active' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}