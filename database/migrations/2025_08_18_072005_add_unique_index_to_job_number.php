<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('master_files', 'job_number')) {
            Schema::table('master_files', function (Blueprint $t) {
                $t->string('job_number', 64)->nullable()->after('product_category');
            });
        }

        $hasIndex = DB::selectOne("
            SELECT 1 FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'master_files'
              AND INDEX_NAME = 'uniq_job_number'
            LIMIT 1
        ");
        if (!$hasIndex) {
            Schema::table('master_files', function (Blueprint $t) {
                $t->unique('job_number', 'uniq_job_number');
            });
        }
    }

    public function down(): void
    {
        try {
            Schema::table('master_files', fn (Blueprint $t) => $t->dropUnique('uniq_job_number'));
        } catch (\Throwable $e) {}
        // Keep the column (non-destructive)
    }
};
    