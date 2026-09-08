<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('mst_user', 'support_group_id')) {
            Schema::table('mst_user', function (Blueprint $table) {
                $table->string('support_group_id', 255)->nullable();
            });
        }

        if (Schema::hasTable('map_user_group')) {
            DB::table('map_user_group')
                ->where('is_active', 1)
                ->select('user_id', 'group_id')
                ->orderBy('mapping_id')
                ->get()
                ->groupBy('user_id')
                ->each(function ($rows, $userId) {
                    DB::table('mst_user')->where('user_id', $userId)->update([
                        'support_group_id' => $rows->pluck('group_id')->unique()->implode(','),
                    ]);
                });
        }

        Schema::dropIfExists('map_user_group');
    }

    public function down(): void
    {
        Schema::table('mst_user', function (Blueprint $table) {
            if (Schema::hasColumn('mst_user', 'support_group_id')) {
                $table->dropColumn('support_group_id');
            }
        });
    }
};