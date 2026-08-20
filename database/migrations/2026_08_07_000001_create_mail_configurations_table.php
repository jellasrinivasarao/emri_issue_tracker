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
        // Mail configuration is managed in the existing database schema.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do not remove the existing mail configuration table.
    }
};
