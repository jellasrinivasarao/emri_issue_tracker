<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('txn_issue', function (Blueprint $table) {
            if (! Schema::hasColumn('txn_issue', 'occurred_date')) {
                $table->date('occurred_date')->nullable()->after('raised_at');
            }
            if (! Schema::hasColumn('txn_issue', 'occurred_time')) {
                $table->time('occurred_time')->nullable()->after('occurred_date');
            }
            if (! Schema::hasColumn('txn_issue', 'affected_users')) {
                $table->string('affected_users', 500)->nullable()->after('occurred_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('txn_issue', function (Blueprint $table) {
            $table->dropColumn(['occurred_date', 'occurred_time', 'affected_users']);
        });
    }
};