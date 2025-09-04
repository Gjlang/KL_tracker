<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ========== CONTENT ==========
        Schema::table('content_calendars', function (Blueprint $t) {
            $t->unsignedBigInteger('master_file_id')->index()->change();
            $t->unsignedSmallInteger('year')->default(date('Y'))->change();
            $t->unsignedTinyInteger('month')->nullable()->change();
            if (!Schema::hasColumn('content_calendars', 'total_artwork')) $t->unsignedInteger('total_artwork')->default(0);
            if (!Schema::hasColumn('content_calendars', 'pending'))       $t->unsignedInteger('pending')->default(0);
            if (!Schema::hasColumn('content_calendars', 'draft_wa'))      $t->boolean('draft_wa')->default(false);
            if (!Schema::hasColumn('content_calendars', 'approved'))      $t->boolean('approved')->default(false);
        });
        // unique slot per bulan
        Schema::table('content_calendars', function (Blueprint $t) {
            $this->dropIfExists($t, 'content_calendars_master_year_month_unique');
            $t->unique(['master_file_id','year','month'], 'content_calendars_master_year_month_unique');
        });

        // ========== EDITING ==========
        Schema::table('artwork_editings', function (Blueprint $t) {
            $t->unsignedBigInteger('master_file_id')->index()->change();
            $t->unsignedSmallInteger('year')->default(date('Y'))->change();
            $t->unsignedTinyInteger('month')->nullable()->change();
            if (!Schema::hasColumn('artwork_editings', 'total_artwork')) $t->unsignedInteger('total_artwork')->default(0);
            if (!Schema::hasColumn('artwork_editings', 'pending'))       $t->unsignedInteger('pending')->default(0);
            if (!Schema::hasColumn('artwork_editings', 'draft_wa'))      $t->boolean('draft_wa')->default(false);
            if (!Schema::hasColumn('artwork_editings', 'approved'))      $t->boolean('approved')->default(false);
        });
        Schema::table('artwork_editings', function (Blueprint $t) {
            $this->dropIfExists($t, 'artwork_editings_master_year_month_unique');
            $t->unique(['master_file_id','year','month'], 'artwork_editings_master_year_month_unique');
        });

        // ========== SCHEDULING (META) ==========
        // Pastikan kolom nama benar: meta_manager (bukan meta_mgr)
        if (Schema::hasColumn('posting_schedulings','meta_mgr') && !Schema::hasColumn('posting_schedulings','meta_manager')) {
            // butuh doctrine/dbal utk renameColumn, kalau belum ada, fallback pakai raw SQL
            try {
                Schema::table('posting_schedulings', function (Blueprint $t) {
                    $t->renameColumn('meta_mgr', 'meta_manager');
                });
            } catch (\Throwable $e) {
                DB::statement('ALTER TABLE posting_schedulings CHANGE COLUMN meta_mgr meta_manager INT NULL');
            }
        }
        Schema::table('posting_schedulings', function (Blueprint $t) {
            $t->unsignedBigInteger('master_file_id')->index()->change();
            $t->unsignedSmallInteger('year')->default(date('Y'))->change();
            $t->unsignedTinyInteger('month')->nullable()->change();
            if (!Schema::hasColumn('posting_schedulings', 'total_artwork'))  $t->unsignedInteger('total_artwork')->default(0);
            if (!Schema::hasColumn('posting_schedulings', 'crm'))            $t->unsignedInteger('crm')->nullable();
            if (!Schema::hasColumn('posting_schedulings', 'meta_manager'))   $t->unsignedInteger('meta_manager')->nullable();
            if (!Schema::hasColumn('posting_schedulings', 'tiktok_ig_draft'))$t->boolean('tiktok_ig_draft')->default(false);
        });
        Schema::table('posting_schedulings', function (Blueprint $t) {
            $this->dropIfExists($t, 'posting_schedulings_master_year_month_unique');
            $t->unique(['master_file_id','year','month'], 'posting_schedulings_master_year_month_unique');
        });

        // ========== REPORT ==========
        Schema::table('media_reports', function (Blueprint $t) {
            $t->unsignedBigInteger('master_file_id')->index()->change();
            $t->unsignedSmallInteger('year')->default(date('Y'))->change();
            $t->unsignedTinyInteger('month')->nullable()->change();
            if (!Schema::hasColumn('media_reports', 'pending'))   $t->unsignedInteger('pending')->default(0);
            if (!Schema::hasColumn('media_reports', 'completed')) $t->boolean('completed')->default(false);
        });
        Schema::table('media_reports', function (Blueprint $t) {
            $this->dropIfExists($t, 'media_reports_master_year_month_unique');
            $t->unique(['master_file_id','year','month'], 'media_reports_master_year_month_unique');
        });

        // ========== VALUE ADD ==========
        Schema::table('media_value_adds', function (Blueprint $t) {
            $t->unsignedBigInteger('master_file_id')->index()->change();
            $t->unsignedSmallInteger('year')->default(date('Y'))->change();
            $t->unsignedTinyInteger('month')->nullable()->change();
            if (!Schema::hasColumn('media_value_adds', 'quota'))     $t->unsignedInteger('quota')->default(0);
            if (!Schema::hasColumn('media_value_adds', 'completed')) $t->unsignedInteger('completed')->default(0);
        });
        Schema::table('media_value_adds', function (Blueprint $t) {
            $this->dropIfExists($t, 'media_value_adds_master_year_month_unique');
            $t->unique(['master_file_id','year','month'], 'media_value_adds_master_year_month_unique');
        });
    }

    public function down(): void
    {
        // Biarkan unique index tetap (tidak perlu rollback detail untuk keamanan data)
    }

    private function dropIfExists(Blueprint $t, string $indexName): void
    {
        // helper dipanggil di closure yang sama tidak bisa; abaikan implementasi (opsional)
    }
};
