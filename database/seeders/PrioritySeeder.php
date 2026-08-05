<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            ['priority_code' => 'LOW', 'priority_name' => 'Low', 'display_order' => 1],
            ['priority_code' => 'MEDIUM', 'priority_name' => 'Medium', 'display_order' => 2],
            ['priority_code' => 'HIGH', 'priority_name' => 'High', 'display_order' => 3],
            ['priority_code' => 'CRITICAL', 'priority_name' => 'Critical', 'display_order' => 4],
        ];

        foreach ($priorities as $priority) {
            DB::table('mst_priority')->updateOrInsert(
                [
                    'priority_code' => $priority['priority_code'],
                ],
                [
                    'priority_name' => $priority['priority_name'],
                    'display_order' => $priority['display_order'],
                    'is_active' => 1,
                ]
            );

        }
    }
}