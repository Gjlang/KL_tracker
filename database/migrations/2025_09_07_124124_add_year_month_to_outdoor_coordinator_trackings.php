<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            if (!Schema::hasColumn('outdoor_coordinator_trackings','year')) {
                $table->smallInteger('year')->unsigned()->nullable()->after('master_file_id');
            }
            if (!Schema::hasColumn('outdoor_coordinator_trackings','month')) {
                $table->tinyInteger('month')->unsigned()->nullable()->after('year');
            }
        });

        // Backfill using created_at; fallback to current month if null
        DB::statement("
            UPDATE outdoor_coordinator_trackings
            SET
              year  = COALESCE(year,  YEAR(COALESCE(created_at, NOW()))),
              month = COALESCE(month, MONTH(COALESCE(created_at, NOW())))
        ");

        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            // Fast filtering
            $table->index(['year','month','master_file_id'], 'oct_year_month_mfid');

            // If you want to enforce 1 row per MF per month, uncomment this:
            // $table->unique(['master_file_id','year','month'], 'oct_mfid_year_month_unique');
        });
    }

    public function down(): void
    {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            $table->dropIndex('oct_year_month_mfid');
            // $table->dropUnique('oct_mfid_year_month_unique');
            $table->dropColumn(['year','month']);
        });
    }
};
