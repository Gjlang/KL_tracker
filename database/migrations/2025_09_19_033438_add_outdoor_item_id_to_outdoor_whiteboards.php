<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('outdoor_whiteboards', function (Blueprint $table) {
            // 1) Add new FK column (nullable for backfill)
            $table->unsignedBigInteger('outdoor_item_id')->nullable()->after('master_file_id');

            // 2) Drop the old unique on master_file_id (name may vary; adjust if needed)
            // If it was created implicitly as unique, it might be named 'outdoor_whiteboards_master_file_id_unique'
            $table->dropUnique('outdoor_whiteboards_master_file_id_unique');

            // 3) Optional: make master_file_id NOT unique, just indexed for filtering
            $table->index('master_file_id');

            // 4) New uniqueness per item (one whiteboard row per site)
            $table->unique('outdoor_item_id');

            // 5) (Optional) FK constraint
            $table->foreign('outdoor_item_id')
                ->references('id')->on('outdoor_items')
                ->onDelete('cascade');
        });

        // ---- Simple backfill idea (temporary): ----
        // If you already had 1 row per master_file_id, assign that row to ONE
        // outdoor_item under the same master_file (e.g., the earliest).
        // Other items will have no whiteboard row until user edits them.
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
            $table->dropForeign(['outdoor_item_id']);
            $table->dropUnique(['outdoor_item_id']);
            $table->dropIndex(['master_file_id']);
            $table->dropColumn('outdoor_item_id');

            // Recreate unique on master_file_id (if you had it before)
            $table->unique('master_file_id');
        });
    }
};
