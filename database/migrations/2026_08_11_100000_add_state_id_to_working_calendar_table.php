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
        Schema::table('mst_working_calendar', function (Blueprint $table) {
            $table->unsignedBigInteger('state_id')->nullable()->after('organisation_id');
            $table->index('state_id');
            $table->foreign('state_id')
                ->references('state_id')
                ->on('mst_state')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_working_calendar', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropIndex(['state_id']);
            $table->dropColumn('state_id');
        });
    }
};
