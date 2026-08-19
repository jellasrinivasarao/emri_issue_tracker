<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $legacyModuleId = 1;

        if (DB::table('mst_module')->where('module_id', $legacyModuleId)->exists()) {
            return;
        }

        $replacementModuleId = DB::table('mst_module')
            ->where('module_name', 'Level 1')
            ->where('is_active', 1)
            ->value('module_id');

        if (! $replacementModuleId) {
            return;
        }

        DB::table('map_project_application_module')
            ->where('module_id', $legacyModuleId)
            ->update(['module_id' => $replacementModuleId]);
    }

    public function down(): void
    {
        // The original module row is unavailable, so this repair cannot be reversed safely.
    }
};