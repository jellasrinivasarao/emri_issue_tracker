<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_role_menu', function (Blueprint $table) {
            $table->id('role_menu_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('menu_id');
            $table->boolean('is_allowed')->default(true);
            $table->timestamps();

            // $table->foreign('role_id')->references('role_id')->on('mst_role')->onDelete('cascade');
            // $table->foreign('menu_id')->references('menu_id')->on('mst_menu')->onDelete('cascade');
            $table->unique(['role_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_role_menu');
    }
};
