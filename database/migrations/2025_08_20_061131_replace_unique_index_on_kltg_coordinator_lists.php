<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $table = 'kltg_coordinator_lists';

        // Find existing index names
        $names = collect(DB::select("SHOW INDEX FROM `$table`"))
            ->pluck('Key_name')
            ->unique()
            ->all();

        // Drop either possible single-column unique if present
        if (in_array('kltg_coord_master_unique', $names)) {
            DB::statement("ALTER TABLE `$table` DROP INDEX `kltg_coord_master_unique`");
        }
        if (in_array('kltg_coordinator_lists_master_file_id_unique', $names)) {
            DB::statement("ALTER TABLE `$table` DROP INDEX `kltg_coordinator_lists_master_file_id_unique`");
        }

        // Add the correct composite unique
        Schema::table($table, function (Blueprint $t) {
            $t->unique(['master_file_id','subcategory'], 'kcl_master_subcategory_unique');
        });
    }

    public function down(): void
    {
        $table = 'kltg_coordinator_lists';
        Schema::table($table, function (Blueprint $t) {
            $t->dropUnique('kcl_master_subcategory_unique');
        });

        // (optional) re-create the old single-column unique
        DB::statement("ALTER TABLE `$table` ADD UNIQUE `kltg_coord_master_unique` (`master_file_id`)");
    }
};

