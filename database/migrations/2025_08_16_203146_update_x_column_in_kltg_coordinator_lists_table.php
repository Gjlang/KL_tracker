<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('kltg_coordinator_lists')) return;

        // 1) Change the column type FIRST (int/tinyint -> varchar).
        //    This converts existing 0/1 to '0'/'1' safely.
        DB::statement("ALTER TABLE kltg_coordinator_lists MODIFY x VARCHAR(50) NULL");

        // 2) Normalize values now that x is a string column.
        //    Map 'false' -> '', keep NULL as NULL, keep '1' as '1'.
        DB::statement("
            UPDATE kltg_coordinator_lists
            SET x = ''   WHERE x IN ('0','false','False')
        ");
        DB::statement("
            UPDATE kltg_coordinator_lists
            SET x = '1' WHERE x IN ('1','true','True')
        ");
    }

    public function down(): void
    {
        if (!Schema::hasTable('kltg_coordinator_lists')) return;

        // Revert to tinyint(1); map non-empty strings to 1 else 0
        DB::statement("
            UPDATE kltg_coordinator_lists
            SET x = CASE WHEN x IS NULL OR x = '' THEN '0' ELSE '1' END
        ");
        DB::statement("ALTER TABLE kltg_coordinator_lists MODIFY x TINYINT(1) NULL");
    }
};
