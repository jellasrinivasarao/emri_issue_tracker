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
        Schema::create('requirement_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('requirement_id')
                ->constrained('requirements')
                ->cascadeOnDelete();

            $table->string('from_status')
                ->nullable();

            $table->string('to_status');

            $table->text('remarks')
                ->nullable();

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'requirement_id',
                'created_at',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_status_histories');
    }
};
