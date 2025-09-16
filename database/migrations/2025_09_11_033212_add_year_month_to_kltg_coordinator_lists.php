<?php
// database/migrations/2025_09_11_000001_add_year_month_to_kltg_coordinator_lists.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('kltg_coordinator_lists', 'year')) {
                $table->integer('year')->nullable()->after('master_file_id');
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'month')) {
                $table->unsignedTinyInteger('month')->nullable()->after('year'); // 1..12
            }

            // Helpful indexes (non-unique for now; uniqueness will be phase 2)
            $table->index(['year', 'month'], 'kcl_year_month_idx');
            $table->index(['master_file_id', 'year', 'month'], 'kcl_mf_year_month_idx');
        });
    }

    public function down(): void
    {
        Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
            // Drop indexes first (ignore if missing)
            try { $table->dropIndex('kcl_mf_year_month_idx'); } catch (\Throwable $e) {}
            try { $table->dropIndex('kcl_year_month_idx'); } catch (\Throwable $e) {}

            // Keep columns by default to avoid destructive rollback on prod.
            // If you truly want to drop them, uncomment:
            // if (Schema::hasColumn('kltg_coordinator_lists', 'month')) $table->dropColumn('month');
            // if (Schema::hasColumn('kltg_coordinator_lists', 'year'))  $table->dropColumn('year');
        });
    }
};

