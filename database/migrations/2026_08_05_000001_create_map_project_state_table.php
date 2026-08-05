<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_project_state', function (Blueprint $table) {
            $table->id('mapping_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('project_id');
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('update_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('state_id')->references('state_id')->on('mst_state')->onDelete('cascade');
            $table->foreign('project_id')->references('project_id')->on('mst_project')->onDelete('cascade');
            $table->unique(['state_id', 'project_id'], 'state_project_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_project_state');
    }
};
