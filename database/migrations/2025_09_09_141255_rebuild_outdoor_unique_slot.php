<?php

// database/migrations/2025_09_09_141100_rebuild_outdoor_unique_slot.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Ensure FKs have their own indexes (so we can drop the composite safely)
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            // New single-column indexes (names unlikely to collide)
            $table->index('master_file_id', 'omd_master_idx');
            $table->index('outdoor_item_id', 'omd_item_idx');
        });

        // 2) Drop the old unique index that doesn't include outdoor_item_id
        //    (use raw SQL to avoid Laravel guessing the wrong name)
        DB::statement('ALTER TABLE `outdoor_monthly_details` DROP INDEX `outdoor_unique_slot`');

        // 3) Recreate unique index including outdoor_item_id (your desired slot key)
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            $table->unique(
                ['master_file_id', 'outdoor_item_id', 'year', 'month', 'field_key'],
                'outdoor_unique_slot'
            );
        });
    }

    public function down(): void
    {
        // Revert to previous state (not recommended, but provided)
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            $table->dropUnique('outdoor_unique_slot');
        });

        // Recreate the old (too-strict) unique (without outdoor_item_id)
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            $table->unique(
                ['master_file_id', 'year', 'month', 'field_key'],
                'outdoor_unique_slot'
            );
        });

        // These helper indexes can stay, but drop them if you want a perfect rollback:
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            $table->dropIndex('omd_master_idx');
            $table->dropIndex('omd_item_idx');
        });
    }
};
