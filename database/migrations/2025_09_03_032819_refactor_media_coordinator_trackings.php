<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Lepas FK & UNIQUE lama di master_file_id (urutan penting)
        Schema::table('media_coordinator_trackings', function (Blueprint $t) {
            // Nama default FK/UNIQUE dari Laravel biasanya seperti di bawah.
            // Kalau di server kamu beda, bisa cek lewat: SHOW CREATE TABLE media_coordinator_trackings;
            try { $t->dropForeign('media_coordinator_trackings_master_file_id_foreign'); } catch (\Throwable $e) {}
            try { $t->dropUnique('media_coordinator_trackings_master_file_id_unique'); } catch (\Throwable $e) {}
        });

        // 2) Tambah kolom year, month, section, payload
        Schema::table('media_coordinator_trackings', function (Blueprint $t) {
            if (!Schema::hasColumn('media_coordinator_trackings', 'year')) {
                $t->unsignedSmallInteger('year')->after('master_file_id');
            }
            if (!Schema::hasColumn('media_coordinator_trackings', 'month')) {
                $t->unsignedTinyInteger('month')->after('year');
            }
            if (!Schema::hasColumn('media_coordinator_trackings', 'section')) {
                $t->enum('section', ['content','editing','schedule','report','valueadd'])->after('month');
            }
            if (!Schema::hasColumn('media_coordinator_trackings', 'payload')) {
                $t->json('payload')->nullable()->after('section');
            }
        });

        // 3) Pasang index normal utk master_file_id (opsional, FK juga bikin index)
        Schema::table('media_coordinator_trackings', function (Blueprint $t) {
            try { $t->index('master_file_id', 'mct_master_file_id_idx'); } catch (\Throwable $e) {}
        });

        // 4) Pasang kembali FOREIGN KEY ke master_files(id)
        Schema::table('media_coordinator_trackings', function (Blueprint $t) {
            try {
                $t->foreign('master_file_id', 'media_coordinator_trackings_master_file_id_foreign')
                  ->references('id')->on('master_files')->cascadeOnDelete();
            } catch (\Throwable $e) {}
        });

        // 5) Tambah UNIQUE gabungan: (master_file_id, year, month, section)
        Schema::table('media_coordinator_trackings', function (Blueprint $t) {
            try {
                $t->unique(['master_file_id','year','month','section'], 'mct_unique_master_year_month_section');
            } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        // Rollback minimal
        Schema::table('media_coordinator_trackings', function (Blueprint $t) {
            try { $t->dropUnique('mct_unique_master_year_month_section'); } catch (\Throwable $e) {}
            try { $t->dropForeign('media_coordinator_trackings_master_file_id_foreign'); } catch (\Throwable $e) {}
            try { $t->dropIndex('mct_master_file_id_idx'); } catch (\Throwable $e) {}

            if (Schema::hasColumn('media_coordinator_trackings', 'payload')) $t->dropColumn('payload');
            if (Schema::hasColumn('media_coordinator_trackings', 'section')) $t->dropColumn('section');
            if (Schema::hasColumn('media_coordinator_trackings', 'month'))   $t->dropColumn('month');
            if (Schema::hasColumn('media_coordinator_trackings', 'year'))    $t->dropColumn('year');

            // (opsional) kalau dulu memang ada UNIQUE tunggal di master_file_id dan ingin dikembalikan:
            // try { $t->unique('master_file_id', 'media_coordinator_trackings_master_file_id_unique'); } catch (\Throwable $e) {}
            // try {
            //     $t->foreign('master_file_id', 'media_coordinator_trackings_master_file_id_foreign')
            //       ->references('id')->on('master_files')->cascadeOnDelete();
            // } catch (\Throwable $e) {}
        });
    }
};
