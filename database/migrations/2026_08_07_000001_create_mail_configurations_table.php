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
        Schema::create('mst_mail_configuration', function (Blueprint $table) {
            $table->increments('mail_configuration_id');
            $table->unsignedInteger('state_id')->nullable();
            $table->string('state_name')->nullable();
            $table->json('to_emails');
            $table->json('cc_emails')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('state_id')->references('state_id')->on('mst_state')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_mail_configuration');
    }
};
