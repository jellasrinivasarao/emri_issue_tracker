<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mst_issue_status', function (Blueprint $table) {
            // Add is_resolved column to track if vendor has resolved the issue
            $table->tinyInteger('is_resolved')->default(0)->after('is_closed_status');
        });

        // Set is_resolved = 1 for "Resolved" status
        \DB::table('mst_issue_status')
            ->where('status_name', 'Resolved')
            ->update(['is_resolved' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_issue_status', function (Blueprint $table) {
            $table->dropColumn('is_resolved');
        });
    }
};
