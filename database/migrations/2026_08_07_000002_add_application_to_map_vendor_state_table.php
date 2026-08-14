<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('map_vendor_state', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->nullable()->after('project_id');
            $table->dropUnique('map_vendor_state_unique');
            $table->unique(['vendor_id', 'state_id', 'project_id', 'application_id'], 'map_vendor_state_unique');
            $table->foreign('application_id', 'fk_map_vendor_state_application')
                ->references('application_id')
                ->on('mst_application')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('map_vendor_state', function (Blueprint $table) {
            $table->dropForeign('fk_map_vendor_state_application');
            $table->dropUnique('map_vendor_state_unique');
            $table->unique(['vendor_id', 'state_id', 'project_id'], 'map_vendor_state_unique');
            $table->dropColumn('application_id');
        });
    }
};
