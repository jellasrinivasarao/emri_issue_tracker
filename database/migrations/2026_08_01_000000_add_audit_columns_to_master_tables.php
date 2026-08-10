<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'mst_state',
            'mst_application',
            'mst_module',
            'mst_project',
            'mst_service',
            'mst_vendor',
            'mst_support_group',
        ];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                $afterColumn = null;

                if (Schema::hasColumn($table, 'created_at')) {
                    $afterColumn = 'created_at';
                }

                if (! Schema::hasColumn($table, 'is_active')) {
                    $column = $tableBlueprint->boolean('is_active')->default(true)->nullable(false);
                    if ($afterColumn) {
                        $column->after($afterColumn);
                    }
                    $afterColumn = 'is_active';
                }

                if (! Schema::hasColumn($table, 'created_at')) {
                    $column = $tableBlueprint->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                    if ($afterColumn) {
                        $column->after($afterColumn);
                    }
                    $afterColumn = 'created_at';
                }

                if (! Schema::hasColumn($table, 'created_by')) {
                    $column = $tableBlueprint->integer('created_by')->nullable();
                    if ($afterColumn) {
                        $column->after($afterColumn);
                    }
                    $afterColumn = 'created_by';
                }

                if (! Schema::hasColumn($table, 'update_at')) {
                    $column = $tableBlueprint->dateTime('update_at')->nullable();
                    if ($afterColumn) {
                        $column->after($afterColumn);
                    }
                    $afterColumn = 'update_at';
                }

                if (! Schema::hasColumn($table, 'updated_by')) {
                    $column = $tableBlueprint->integer('updated_by')->nullable();
                    if ($afterColumn) {
                        $column->after($afterColumn);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'mst_state',
            'mst_application',
            'mst_module',
            'mst_project',
            'mst_service',
            'mst_vendor',
            'mst_support_group',
        ];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                if (Schema::hasColumn($table, 'updated_by')) {
                    $tableBlueprint->dropColumn('updated_by');
                }

                if (Schema::hasColumn($table, 'update_at')) {
                    $tableBlueprint->dropColumn('update_at');
                }

                if (Schema::hasColumn($table, 'created_by')) {
                    $tableBlueprint->dropColumn('created_by');
                }

                if (Schema::hasColumn($table, 'created_at')) {
                    $tableBlueprint->dropColumn('created_at');
                }

                if (Schema::hasColumn($table, 'is_active')) {
                    $tableBlueprint->dropColumn('is_active');
                }
            });
        }
    }
};
