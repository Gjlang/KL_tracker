<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('outdoor_whiteboards', function (Blueprint $table) {
            // 1) Add new FK column (nullable for backfill)
            if (!Schema::hasColumn('outdoor_whiteboards', 'outdoor_item_id')) {
                $table->unsignedBigInteger('outdoor_item_id')->nullable()->after('master_file_id');
            }
        });

        // 2) Drop foreign key on master_file_id first (if exists)
        try {
            Schema::table('outdoor_whiteboards', function (Blueprint $table) {
                $table->dropForeign(['master_file_id']);
            });
        } catch (\Throwable $e) {
            // Foreign key tak wujud, skip
        }

        // 3) Now drop the unique index
        try {
            DB::statement('ALTER TABLE outdoor_whiteboards DROP INDEX outdoor_whiteboards_master_file_id_unique');
        } catch (\Throwable $e) {
            // Index tak wujud, skip
        }

        Schema::table('outdoor_whiteboards', function (Blueprint $table) {
            // 4) Make master_file_id just indexed for filtering
            $table->index('master_file_id', 'outdoor_whiteboards_master_file_id_index');

            // 5) New uniqueness per item (one whiteboard row per site)
            $table->unique('outdoor_item_id', 'outdoor_whiteboards_outdoor_item_id_unique');

            // 6) Recreate master_file_id FK
            $table->foreign('master_file_id')
                ->references('id')->on('master_files')
                ->onDelete('cascade');

            // 7) Add outdoor_item_id FK constraint
            $table->foreign('outdoor_item_id')
                ->references('id')->on('outdoor_items')
                ->onDelete('cascade');
        });

        // ---- Backfill ----
        DB::statement("
            UPDATE outdoor_whiteboards ow
            JOIN (
                SELECT oi.master_file_id, MIN(oi.id) AS first_item_id
                FROM outdoor_items oi
                GROUP BY oi.master_file_id
            ) x ON x.master_file_id = ow.master_file_id
            SET ow.outdoor_item_id = x.first_item_id
            WHERE ow.outdoor_item_id IS NULL
        ");
    }

    public function down(): void {
        Schema::table('outdoor_whiteboards', function (Blueprint $table) {
            // Remove new constraints/indexes
            try { $table->dropForeign(['outdoor_item_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['master_file_id']); } catch (\Throwable $e) {}
            try { $table->dropUnique('outdoor_whiteboards_outdoor_item_id_unique'); } catch (\Throwable $e) {}
            try { $table->dropIndex('outdoor_whiteboards_master_file_id_index'); } catch (\Throwable $e) {}
            try { $table->dropColumn('outdoor_item_id'); } catch (\Throwable $e) {}
        });

        // Recreate unique on master_file_id
        Schema::table('outdoor_whiteboards', function (Blueprint $table) {
            $table->unique('master_file_id', 'outdoor_whiteboards_master_file_id_unique');

            // Recreate FK
            $table->foreign('master_file_id')
                ->references('id')->on('master_files')
                ->onDelete('cascade');
        });
    }
};
