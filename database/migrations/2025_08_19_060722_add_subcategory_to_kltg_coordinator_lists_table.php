<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
            // keep it nullable to avoid breaking existing inserts;
            // controller will require it on new writes
            if (!Schema::hasColumn('kltg_coordinator_lists', 'subcategory')) {
                $table->string('subcategory', 20)->nullable()->after('master_file_id');
            }

            // speed up lookups and unique row per master + subcategory
            $table->index(['master_file_id', 'subcategory'], 'kltg_coord_master_subcat_idx');
        });
    }

    public function down(): void
    {
        Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
            if (Schema::hasColumn('kltg_coordinator_lists', 'subcategory')) {
                $table->dropIndex('kltg_coord_master_subcat_idx');
                $table->dropColumn('subcategory');
            }
        });
    }
};
