<?php

// database/migrations/2025_09_07_000001_add_index_to_outdoor_monthly_details.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('outdoor_monthly_details')) {
            Schema::table('outdoor_monthly_details', function (Blueprint $table) {
                if (! collect($table->getIndexes() ?? [])->contains('omd_year_month_mfid')) {
                    $table->index(['year','month','master_file_id'], 'omd_year_month_mfid');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('outdoor_monthly_details')) {
            Schema::table('outdoor_monthly_details', function (Blueprint $table) {
                $table->dropIndex('omd_year_month_mfid');
            });
        }
    }
};
