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
        Schema::create('mst_sla_configuration', function (Blueprint $table) {
            
            $table->bigIncrements('sla_configuration_id');

            /*
            |--------------------------------------------------------------------------
            | SLA Identification
            |--------------------------------------------------------------------------
            */

            $table->string('sla_code', 50)
                ->unique();

            $table->string('sla_name', 150);

            $table->text('description')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | SLA Timing
            |--------------------------------------------------------------------------
            |
            | All values are business hours, not calendar hours.
            |
            */

            $table->decimal('response_sla_hours', 8, 2)
                ->default(0);

            $table->decimal('resolution_sla_hours', 8, 2)
                ->default(0);

            $table->decimal('escalation_sla_hours', 8, 2)
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Support / Escalation
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('support_level')
                ->nullable()
                ->comment('1=HO IT Level-1, 2=Vendor Level-2');


            /*
            |--------------------------------------------------------------------------
            | Working Calendar
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('working_calendar_id')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);


            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->unsignedBigInteger('updated_by')
                ->nullable();

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                'working_calendar_id',
                'idx_sla_working_calendar'
            );

            $table->index(
                'support_level',
                'idx_sla_support_level'
            );

            $table->index(
                'is_active',
                'idx_sla_active'
            );


            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            |
            | If your existing working calendar PK is different,
            | change this reference accordingly.
            |
            */

            $table->foreign('working_calendar_id')
                ->references('working_calendar_id')
                ->on('mst_working_calendar')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_sla_configuration');
    }
};