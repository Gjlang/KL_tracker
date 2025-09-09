<?php

// database/migrations/2025_09_09_000001_add_outdoor_item_id_to_outdoor_coordinator_trackings.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            $table->unsignedBigInteger('outdoor_item_id')->nullable()->after('master_file_id')->index();
            // Optional: if you already have the foreign table/model
            // $table->foreign('outdoor_item_id')->references('id')->on('outdoor_items')->nullOnDelete();
        });

        // (Optional backfill) If you previously stored site text in OCT,
        // try to map by matching site text -> outdoor_items.site for the same master_file_id.
        DB::statement("
            UPDATE outdoor_coordinator_trackings oct
            JOIN outdoor_items oi
              ON oi.master_file_id = oct.master_file_id
             AND TRIM(LOWER(oi.site)) = TRIM(LOWER(oct.site))
            SET oct.outdoor_item_id = oi.id
            WHERE oct.outdoor_item_id IS NULL
        ");

        // Prevent duplicates per site
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            $table->unique(['master_file_id', 'outdoor_item_id'], 'oct_master_outdoor_unique');
        });
    }

    public function down(): void
    {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            $table->dropUnique('oct_master_outdoor_unique');
            $table->dropColumn('outdoor_item_id');
        });
    }
};
