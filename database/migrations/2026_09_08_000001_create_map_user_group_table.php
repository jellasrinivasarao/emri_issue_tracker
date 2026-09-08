<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_user_group', function (Blueprint $table) {
            $table->id('mapping_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('group_id');
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable();
            $table->unique(['user_id', 'group_id'], 'user_group_unique');
            $table->foreign('user_id')->references('user_id')->on('mst_user')->onDelete('cascade');
            $table->foreign('group_id')->references('group_id')->on('mst_group')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_user_group');
    }
};