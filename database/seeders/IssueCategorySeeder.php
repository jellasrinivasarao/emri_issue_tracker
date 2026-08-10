<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IssueCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $issueCategories = [
            'Application Issue',
            'Infrastructure Issue',
            'Network Issue',
            'Access Issue',
            'Data Issue',
            'Hardware Issue',
            'Other',
        ];

        foreach ($issueCategories as $category) {
            DB::table('mst_issue_category')->updateOrInsert(
                [
                    'category_code' => Str::upper(Str::snake($category)),
                ],
                [
                    'category_name' => $category,
                    'description' => $category,
                    'is_active' => 1,
                ]
            );
        }
    }
}