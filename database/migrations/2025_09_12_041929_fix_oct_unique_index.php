<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Drop the old unique. If the name differs, run `SHOW INDEX FROM outdoor_coordinator_trackings;`
        // and adjust the name below.
        DB::statement("ALTER TABLE `outdoor_coordinator_trackings` DROP INDEX `oct_master_outdoor_unique`");

        Schema::table('outdoor_coordinator_trackings', function (Blueprint $t) {
            // The correct uniqueness for month-scoped rows:
            $t->unique(['master_file_id','outdoor_item_id','year','month'], 'oct_mf_item_year_month_unique');

            // Helpful secondary indexes
            $t->index(['outdoor_item_id']);
            $t->index(['year','month']);
        });
    }

    public function down(): void
    {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $t) {
            $t->dropUnique('oct_mf_item_year_month_unique');
            // Restore the old behavior if you ever need to roll back:
            $t->unique(['master_file_id','outdoor_item_id'], 'oct_master_outdoor_unique');
        });
    }
};
