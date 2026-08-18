<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapIssueVendorAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_issue_vendor_assignment', function (Blueprint $table) {
            $table->id('issue_vendor_assignment_id');
            $table->unsignedBigInteger('issue_id');
            $table->unsignedBigInteger('vendor_id');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('issue_id')->references('issue_id')->on('txn_issue')->onDelete('cascade');
            $table->foreign('vendor_id')->references('vendor_id')->on('mst_vendor')->onDelete('cascade');
            $table->unique(['issue_id', 'vendor_id'], 'map_issue_vendor_assignment_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('map_issue_vendor_assignment');
    }
}
