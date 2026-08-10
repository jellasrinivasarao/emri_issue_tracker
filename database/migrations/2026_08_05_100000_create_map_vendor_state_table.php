<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_vendor_state', function (Blueprint $table) {
            $table->bigIncrements('mapping_id');
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('project_id');
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('update_at')->nullable();
            $table->unique(['vendor_id', 'state_id', 'project_id'], 'map_vendor_state_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_vendor_state');
    }
};
