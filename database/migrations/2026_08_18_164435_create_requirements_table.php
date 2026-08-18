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
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            
            $table->string('requirement_no')->unique();

            $table->string('title');

            $table->text('description');

            $table->foreignId('state_id')
                ->constrained('states')
                ->restrictOnDelete();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->restrictOnDelete();

            $table->string('brd_raised_by')
                ->nullable();

            $table->boolean('ho_it_team')
                ->default(true);

            $table->date('received_at')
                ->nullable();

            $table->date('requested_to_vendor_at')
                ->nullable();

            $table->text('additional_details')
                ->nullable();

            /*
             * Vendor information
             */
            $table->foreignId('assigned_vendor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('man_days', 10, 2)
                ->nullable();

            $table->text('timeline')
                ->nullable();

            $table->string('delivery_status')
                ->nullable();

            $table->text('vendor_remarks')
                ->nullable();

            /*
             * Requirement lifecycle
             */
            $table->string('status')
                ->default('BRD Raised')
                ->index();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->softDeletes();

            $table->index([
                'state_id',
                'status',
            ]);

            $table->index([
                'project_id',
                'status',
            ]);

            $table->index('created_at');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
