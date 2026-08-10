<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_project_application_module', function (Blueprint $table) {
            $table->id('mapping_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('module_id');
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('update_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('project_id')->references('project_id')->on('mst_project')->onDelete('cascade');
            $table->foreign('application_id')->references('application_id')->on('mst_application')->onDelete('cascade');
            $table->foreign('module_id')->references('module_id')->on('mst_application_module')->onDelete('cascade');
            $table->unique(['project_id', 'application_id', 'module_id'], 'project_application_module_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_project_application_module');
    }
};
