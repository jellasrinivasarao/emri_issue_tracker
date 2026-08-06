<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_mail_log', function (Blueprint $table) {
            $table->increments('mail_log_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('to_address');
            $table->string('subject');
            $table->text('body')->nullable();
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->string('mailer')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('to_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_mail_log');
    }
};
