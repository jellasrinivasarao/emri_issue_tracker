<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_group', function (Blueprint $table) {
            $table->id('group_id');
            $table->string('group_name', 150)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });

        Schema::create('map_group_project_application', function (Blueprint $table) {
            $table->id('mapping_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('application_id');
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('group_id')->references('group_id')->on('mst_group')->onDelete('cascade');
            $table->foreign('project_id')->references('project_id')->on('mst_project')->onDelete('cascade');
            $table->foreign('application_id')->references('application_id')->on('mst_application')->onDelete('cascade');
            $table->unique(['group_id', 'project_id', 'application_id'], 'group_project_application_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_group_project_application');
        Schema::dropIfExists('mst_group');
    }
};