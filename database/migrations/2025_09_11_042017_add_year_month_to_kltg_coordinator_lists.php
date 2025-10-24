<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('kltg_coordinator_lists', function (Blueprint $t) {
            if (!Schema::hasColumn('kltg_coordinator_lists','year')) {
                $t->integer('year')->nullable()->after('master_file_id');
            }
            if (!Schema::hasColumn('kltg_coordinator_lists','month')) {
                $t->unsignedTinyInteger('month')->nullable()->after('year');
            }
        });

        // Drop index kalau ada, then recreate
        try {
            DB::statement('ALTER TABLE kltg_coordinator_lists DROP INDEX kcl_year_month_idx');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE kltg_coordinator_lists DROP INDEX kcl_mf_year_month_idx');
        } catch (\Throwable $e) {}

        // Create indexes
        Schema::table('kltg_coordinator_lists', function (Blueprint $t) {
            $t->index(['year','month'],'kcl_year_month_idx');
            $t->index(['master_file_id','year','month'],'kcl_mf_year_month_idx');
        });

        // Hapus duplikat
        DB::statement("
            DELETE t1 FROM kltg_coordinator_lists t1
            JOIN kltg_coordinator_lists t2
              ON t1.id < t2.id
             AND COALESCE(t1.master_file_id,0)=COALESCE(t2.master_file_id,0)
             AND COALESCE(t1.subcategory,'')=COALESCE(t2.subcategory,'')
             AND COALESCE(t1.year,0)=COALESCE(t2.year,0)
             AND COALESCE(t1.month,0)=COALESCE(t2.month,0)
        ");

        // Drop unique kalau ada
        try {
            DB::statement('ALTER TABLE kltg_coordinator_lists DROP INDEX kcl_unique_slot');
        } catch (\Throwable $e) {}

        Schema::table('kltg_coordinator_lists', function (Blueprint $t) {
            $t->unique(['master_file_id','subcategory','year','month'],'kcl_unique_slot');
        });
    }

    public function down(): void {
        Schema::table('kltg_coordinator_lists', function (Blueprint $t) {
            try { $t->dropUnique('kcl_unique_slot'); } catch (\Throwable $e) {}
            try { $t->dropIndex('kcl_mf_year_month_idx'); } catch (\Throwable $e) {}
            try { $t->dropIndex('kcl_year_month_idx'); } catch (\Throwable $e) {}
            try { $t->dropColumn(['year','month']); } catch (\Throwable $e) {}
        });
    }
};
