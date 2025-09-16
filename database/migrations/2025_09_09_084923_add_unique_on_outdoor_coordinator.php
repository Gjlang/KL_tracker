<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueOnOutdoorCoordinator extends Migration
{
    public function up(): void
    {
        // OPTIONAL: clean duplicates before adding unique index (keep the smallest id per pair)
        // Comment this block out if you don't want auto-clean.
        DB::statement("
            DELETE t1 FROM outdoor_coordinator_trackings t1
            JOIN outdoor_coordinator_trackings t2
              ON t1.master_file_id = t2.master_file_id
             AND COALESCE(t1.site,'') = COALESCE(t2.site,'')
             AND t1.id > t2.id
        ");

        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            // Only add the index if it doesn't exist yet (works for MySQL)
            // Laravel doesn't have hasIndex; do a raw check:
        });

        // Raw check + add unique (MySQL)
        $exists = DB::selectOne("
            SELECT COUNT(1) AS cnt
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = 'outdoor_coordinator_trackings'
              AND index_name = 'uct_master_site_unique'
        ");

        if (!$exists || (int)$exists->cnt === 0) {
            DB::statement("
                ALTER TABLE outdoor_coordinator_trackings
                ADD UNIQUE KEY uct_master_site_unique (master_file_id, site)
            ");
        }
    }

    public function down(): void
    {
        // Drop unique key if exists
        $exists = DB::selectOne("
            SELECT COUNT(1) AS cnt
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = 'outdoor_coordinator_trackings'
              AND index_name = 'uct_master_site_unique'
        ");

        if ($exists && (int)$exists->cnt > 0) {
            DB::statement("
                ALTER TABLE outdoor_coordinator_trackings
                DROP INDEX uct_master_site_unique
            ");
        }
    }
}
