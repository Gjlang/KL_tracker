<?php

// database/migrations/2025_09_16_000001_add_expected_finish_date_to_client_feed_backlogs.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('client_feed_backlogs', function (Blueprint $table) {
            // Check column dulu
            if (!Schema::hasColumn('client_feed_backlogs', 'expected_finish_date')) {
                $table->date('expected_finish_date')->nullable()->after('date');
            }
        });

        // Drop index kalau ada, then create
        try {
            DB::statement('ALTER TABLE client_feed_backlogs DROP INDEX client_feed_backlogs_expected_finish_date_index');
        } catch (\Throwable $e) {}

        Schema::table('client_feed_backlogs', function (Blueprint $table) {
            $table->index('expected_finish_date', 'client_feed_backlogs_expected_finish_date_index');
        });
    }

    public function down(): void {
        Schema::table('client_feed_backlogs', function (Blueprint $table) {
            try { $table->dropIndex('client_feed_backlogs_expected_finish_date_index'); } catch (\Throwable $e) {}
            try { $table->dropColumn('expected_finish_date'); } catch (\Throwable $e) {}
        });
    }
};
