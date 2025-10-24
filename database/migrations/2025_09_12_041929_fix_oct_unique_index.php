<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Drop indexes yang mungkin dah wujud
        try {
            DB::statement("ALTER TABLE `outdoor_coordinator_trackings` DROP INDEX `oct_master_outdoor_unique`");
        } catch (\Throwable $e) {
            // Index tak wujud, skip
        }

        try {
            DB::statement("ALTER TABLE `outdoor_coordinator_trackings` DROP INDEX `outdoor_coordinator_trackings_outdoor_item_id_index`");
        } catch (\Throwable $e) {
            // Index tak wujud, skip
        }

        try {
            DB::statement("ALTER TABLE `outdoor_coordinator_trackings` DROP INDEX `oct_mf_item_year_month_unique`");
        } catch (\Throwable $e) {
            // Index tak wujud, skip
        }

        Schema::table('outdoor_coordinator_trackings', function (Blueprint $t) {
            // The correct uniqueness for month-scoped rows:
            $t->unique(['master_file_id','outdoor_item_id','year','month'], 'oct_mf_item_year_month_unique');

            // Helpful secondary indexes
            $t->index(['outdoor_item_id'], 'outdoor_coordinator_trackings_outdoor_item_id_index');
            $t->index(['year','month'], 'oct_year_month_idx');
        });
    }

    public function down(): void
    {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $t) {
            try { $t->dropUnique('oct_mf_item_year_month_unique'); } catch (\Throwable $e) {}
            try { $t->dropIndex('outdoor_coordinator_trackings_outdoor_item_id_index'); } catch (\Throwable $e) {}
            try { $t->dropIndex('oct_year_month_idx'); } catch (\Throwable $e) {}

            // Restore the old behavior if you ever need to roll back:
            $t->unique(['master_file_id','outdoor_item_id'], 'oct_master_outdoor_unique');
        });
    }
};
