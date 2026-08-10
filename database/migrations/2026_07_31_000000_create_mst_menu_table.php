<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_menu', function (Blueprint $table) {
            $table->id('menu_id');
            $table->string('display_name', 150);
            $table->string('route_name', 150)->unique();
            $table->string('uri', 255)->nullable();
            $table->unsignedBigInteger('parent_menu_id')->nullable();
            $table->string('icon', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('parent_menu_id')->references('menu_id')->on('mst_menu')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_menu');
    }
};
